<?php

/**
 * Qubus\Error
 *
 * @link       https://github.com/QubusPHP/error
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Error\Handlers;

use ErrorException;
use Psr\Log\LoggerInterface;
use Throwable;

final class ProductionErrorHandler implements ErrorHandler
{
    public const ERROR_HANDLER = 'registerErrorHandler';

    public const EXCEPTION_HANDLER = 'registerExceptionHandler';

    public const SHUTDOWN_HANDLER = 'registerShutdownHandler';
    
    /** @var LoggerInterface[] $loggers */
    private array $loggers = [];

    /** @var bool $displayErrors */
    protected bool $displayErrors;

    /** @var bool $notifyPsr3Loggers */
    protected bool $notifyPsr3Loggers;

    /** @var bool $notifyNativePhpLog */
    protected bool $notifyNativePhpLog;

    public function __construct(
        bool $displayErrors = false,
        bool $notifyPsr3Loggers = false,
        bool $notifyNativePhpLog = true
    ) {
        $this->displayErrors = $displayErrors;
        $this->notifyPsr3Loggers = $notifyPsr3Loggers;
        $this->notifyNativePhpLog = $notifyNativePhpLog;

        $this->registerAllHandlers();
    }

    /**
     * Adds a logger to the ExceptionHandler.
     *
     * @param LoggerInterface $logger
     */
    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    /**
     * Logs an error to PHP's native error_log() and PSR3 loggers.
     *
     * @param Throwable $throwable
     * @param bool $isEmergency Used only for uncaught exceptions.
     */
    public function log(Throwable $throwable, bool $isEmergency = false): void
    {
        if ($this->notifyNativePhpLog) {
            // Write error to standard php log.
            error_log(message: (string) $throwable);
        }

        // Trigger PSR3 loggers? For example when in test environment we don't need to spam (production) logs.
        if (!$this->notifyPsr3Loggers) {
            return;
        }

        // Add some extra info if it was a web request.
        $message = '';
        if (PHP_SAPI !== 'cli') {
            $message .= sprintf(
                'Method: %s, %s%sURL: %s://%s%s%sUser-Agent: %s%s',
                filter_input(type: INPUT_SERVER, var_name: 'SERVER_PROTOCOL', filter: FILTER_DEFAULT),
                filter_input(type: INPUT_SERVER, var_name: 'REQUEST_METHOD', filter: FILTER_DEFAULT),
                PHP_EOL,
                filter_input(type: INPUT_SERVER, var_name: 'REQUEST_SCHEME', filter: FILTER_DEFAULT),
                filter_input(type: INPUT_SERVER, var_name: 'HTTP_HOST', filter: FILTER_DEFAULT),
                filter_input(type: INPUT_SERVER, var_name: 'REQUEST_URI', filter: FILTER_DEFAULT),
                PHP_EOL,
                filter_input(type: INPUT_SERVER, var_name: 'HTTP_USER_AGENT', filter: FILTER_DEFAULT),
                PHP_EOL . PHP_EOL
            );
        }
        $message .= $throwable;

        foreach ($this->loggers as $logger) {
            try {
                if ($isEmergency) {
                    $logger->emergency($message);
                } else {
                    $logger->error($message);
                }
            } catch (Throwable $throwable) {
                // Notify PHP's native error_log() if other logger's have died.
                error_log((string)$throwable);
            }
        }
    }

    /**
     * Converts php notices/warnings/errors to ErrorException and throws it.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @throws ErrorException
     */
    public function registerErrorHandler(int $errno, string $errstr, string $errfile, int $errline): void
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler.
            return;
        }

        throw new ErrorException(message: $errstr, code: 0, severity: $errno, filename: $errfile, line: $errline);
    }

    /**
     * Handle uncaught exceptions.
     *
     * @param Throwable $throwable
     * @throws Throwable
     */
    public function registerExceptionHandler(Throwable $throwable): void
    {
        // Send $throwable to logs.
        $this->log(throwable: $throwable, isEmergency: true);

        // Dump on screen when running in a test environment.
        if ($this->displayErrors) {
            if (PHP_SAPI === 'cli') {
                echo $throwable . PHP_EOL;
            } else {
                http_response_code(response_code: 500);
                // @todo Should we use Symfony's var dumper here?
                echo '<pre>' . htmlspecialchars(string: (string) $throwable) . '</pre>';
            }

            return;
        }

        // Triggers web server default 500 error page.
        throw $throwable;
    }

    /**
     * When php shuts down, check if this is caused by a(n) (fatal) error.
     * If so convert and catch it to have it processed like the rest of the exceptions.
     *
     * @throws Throwable
     */
    public function registerShutdownHandler()
    {
        $error = error_get_last();
        if (!empty($error) && is_array(value: $error)) {
            try {
                $this->registerErrorHandler(
                    errno: $error['type'],
                    errstr: $error['message'],
                    errfile: $error['file'],
                    errline: $error['line']
                );
            } catch (Throwable $throwable) {
                $this->registerExceptionHandler(throwable: $throwable);
            }
        }
    }

    private function registerAllHandlers()
    {
        // Force error reporting to always be on. But hide it for the user.
        error_reporting(error_level: E_ALL);
        ini_set(option: 'display_errors', value: 0);
        ini_set(option: 'display_startup_errors', value: 0);

        set_error_handler(callback: [$this, self::ERROR_HANDLER], error_levels: E_ALL);
        set_exception_handler(callback: [$this, self::EXCEPTION_HANDLER]);
        register_shutdown_function(callback: [$this, self::SHUTDOWN_HANDLER]);
    }

    /**
     * De-registers the error handling functions, returning them to their previous state.
     */
    public function unregister(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }
}
