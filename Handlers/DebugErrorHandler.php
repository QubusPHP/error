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

use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;
use const E_CORE_ERROR;
use const E_CORE_WARNING;
use const E_DEPRECATED;
use const E_ERROR;
use const E_NOTICE;
use const E_PARSE;
use const E_RECOVERABLE_ERROR;
use const E_STRICT;
use const E_USER_DEPRECATED;
use const E_USER_ERROR;
use const E_USER_NOTICE;
use const E_USER_WARNING;
use const E_WARNING;

final class DebugErrorHandler implements ErrorHandler
{
    /**
     * Active stack.
     */
    public array $stack;

    /**
     * Style load validator.
     */
    public bool $styles = false;

    /**
     * Custom methods.
     */
    public bool $customMethods = false;

    /**
     * Catch errors and exceptions and execute the method.
     */
    public function __construct()
    {
        $this->registerAllHandlers();
    }

    /**
     * Handle exceptions catch.
     *
     * @param object $e
     *                  string $e->getMessage()       → exception message
     *                  int    $e->getCode()          → exception code
     *                  string $e->getFile()          → file
     *                  int    $e->getLine()          → line
     *                  string $e->getTraceAsString() → trace as string
     *                  int    $e->statusCode         → HTTP response status code
     */
    public function registerExceptionHandler(object $e): bool
    {
        $traceString = preg_split(pattern: "/#[\d]/", subject: $e->getTraceAsString());

        unset($traceString[0]);
        array_pop($traceString);

        $trace = "\r\n<hr>BACKTRACE:\r\n";

        foreach ($traceString as $key => $value) {
            $trace .= "\n" . $key . ' ·' . $value;
        }

        $this->setParams(
            type: 'Exception',
            code: $e->getCode(),
            msg: $e->getMessage(),
            file: $e->getFile(),
            line: $e->getLine(),
            trace: $trace,
            http: (isset($e->statusCode)) ? $e->statusCode : 0
        );

        return $this->render();
    }

    /**
     * Handle error catch.
     *
     * @param int $code → error code
     * @param string $msg  → error message
     * @param string $file → error file
     * @param int $line → error line
     *
     * @return bool
     */
    public function registerErrorHandler(int $code, string $msg, string $file, int $line): bool
    {
        $type = $this->getErrorType(code: $code);

        $this->setParams(type: $type, code: $code, msg: $msg, file: $file, line: $line, trace: '', http: 0);

        return $this->render();
    }

    /**
     * Convert error code to text.
     *
     * @param int $code → error code
     * @return string → error type
     */
    public function getErrorType(int $code): string
    {
        return $this->stack['type'] = match ($code) {
            E_WARNING => 'Warning', // 2
            E_PARSE => 'Parse', // 4
            E_NOTICE => 'Notice', // 8
            E_CORE_ERROR => 'Core-Error', // 16
            E_CORE_WARNING => 'Core Warning', // 32
            E_COMPILE_ERROR => 'Compile Error', // 64
            E_COMPILE_WARNING => 'Compile Warning', // 128
            E_USER_ERROR => 'User Error', // 256
            E_USER_WARNING => 'User Warning', // 512
            E_USER_NOTICE => 'User Notice', // 1024
            E_STRICT => 'Strict', // 2048
            E_RECOVERABLE_ERROR => 'Recoverable Error', // 4096
            E_DEPRECATED => 'Deprecated', // 8192
            E_USER_DEPRECATED => 'User Deprecated', // 16384
            default => 'Error',
        };
    }

    /**
     * Set customs methods to renderizate.
     *
     * @param object|string $class   → class name or class object
     * @param string $method  → method name
     * @param int $repeat  → number of times to repeat method
     * @param bool $default → show default view
     */
    public function setCustomMethod(object|string $class, string $method, int $repeat = 0, bool $default = false)
    {
        $this->customMethods[] = [$class, $method, $repeat, $default];
    }

    /**
     * Handle error catch.
     *
     * @param string $type
     * @param mixed $code  → exception/error code
     * @param string $msg   → exception/error message
     * @param string $file  → exception/error file
     * @param int $line  → exception/error line
     * @param string $trace → exception/error trace
     * @param int $http  → HTTP response status code
     *
     * @return array → stack
     */
    protected function setParams(
        string $type,
        mixed $code,
        string $msg,
        string $file,
        int $line,
        string $trace,
        int $http
    ): array {
        return $this->stack = [
            'type' => $type,
            'message' => $msg,
            'file' => $file,
            'line' => $line,
            'code' => $code,
            'http-code' => ($http === 0) ? http_response_code() : $http,
            'trace' => $trace,
            'preview' => '',
        ];
    }

    /**
     * Get preview of the error line.
     */
    protected function getPreviewCode()
    {
        $file = file(filename: $this->stack['file']);
        $line = $this->stack['line'];

        $start = ($line - 5 >= 0) ? $line - 5 : $line - 1;
        $end = ($line - 5 >= 0) ? $line + 4 : $line + 8;

        for ($i = $start; $i < $end; $i++) {
            if (! isset($file[$i])) {
                continue;
            }

            $text = trim(string: $file[$i]);

            if ($i == $line - 1) {
                $this->stack['preview'] .=
                    "<span class='jst-line'>" . ($i + 1) . '</span>' .
                    "<span class='jst-mark text'>" . $text . '</span><br>';
                continue;
            }

            $this->stack['preview'] .=
                "<span class='jst-line'>" . ($i + 1) . '</span>' .
                "<span class='text'>" . $text . '</span><br>';
        }
    }

    /**
     * Get customs methods to renderizate.
     */
    protected function getCustomMethods(): bool
    {
        $showDefaultView = true;
        $params = [$this->stack];

        unset($params[0]['trace'], $params[0]['preview']);

        $count = count($this->customMethods);
        $customMethods = $this->customMethods;

        for ($i = 0; $i < $count; $i++) {
            $custom = $customMethods[$i];
            $class = isset($custom[0]) ? $custom[0] : false;
            $method = isset($custom[1]) ? $custom[1] : false;
            $repeat = $custom[2];
            $showDefault = $custom[3];

            if ($showDefault === false) {
                $showDefaultView = false;
            }

            if ($repeat === 0) {
                unset($this->customMethods[$i]);
            } else {
                $this->customMethods[$i] = [$class, $method, $repeat--];
            }

            call_user_func_array([$class, $method], $params);
        }

        $this->customMethods = false;

        return $showDefaultView;
    }

    /**
     * Renderization.
     *
     * @return bool
     */
    protected function render(): bool
    {
        $this->stack['mode'] = 'PHP';

        if ($this->customMethods && ! $this->getCustomMethods()) {
            return false;
        }

        $this->getPreviewCode();

        if (! $this->styles) {
            $this->styles = true;
            $this->stack['css'] = require __DIR__ . '/public/css/styles.html';
        }

        $stack = $this->stack;

        require __DIR__ . '/public/template/view.php';

        return true;
    }

    protected function registerAllHandlers(): void
    {
        set_exception_handler([$this, 'registerExceptionHandler']);
        set_error_handler([$this, 'registerErrorHandler']);
    }
}
