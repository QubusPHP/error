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

final class DebugErrorHandler implements ErrorHandler
{
    public \Whoops\Run $whoops;

    /**
     * Catch errors and exceptions and execute the method.
     */
    public function __construct(private readonly string $title = 'QubusPHP Error')
    {
        $this->registerHandler();
    }

    /**
     * Handle error catch.
     *
     * @return void
     */
    public function registerErrorHandler(): void
    {
        $this->whoops = new \Whoops\Run();

        $Handler = new \Whoops\Handler\PrettyPageHandler();
        $Handler->setPageTitle(title: $this->title);

        $this->whoops->pushHandler($Handler);
        $this->whoops->register();
    }

    protected function registerHandler(): void
    {
        $this->registerErrorHandler();
    }
}
