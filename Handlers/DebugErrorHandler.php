<?php

/**
 * Qubus\Error
 *
 * @link       https://github.com/QubusPHP/error
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Qubus\Error\Handlers;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class DebugErrorHandler implements ErrorHandler
{
    public Run $whoops;
    /**
     * Catch errors and exceptions and execute the method.
     */
    public function __construct(private readonly string $title = 'QubusPHP Error')
    {
        $this->registerAllHandlers();
    }

    /**
     * Handle error catch.
     *
     * @return void
     */
    public function registerErrorHandler(): void
    {
        $this->whoops = new Run();

        $Handler = new PrettyPageHandler();
        $Handler->setPageTitle(title: $this->title);

        $this->whoops->pushHandler($Handler);
        $this->whoops->register();
    }

    protected function registerAllHandlers(): void
    {
        set_exception_handler([$this, 'registerErrorHandler']);
        set_error_handler([$this, 'registerErrorHandler']);
    }
}
