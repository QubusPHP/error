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

namespace Qubus\Error;

use Psr\Log\LoggerInterface;
use Qubus\Error\Handlers\DebugErrorHandler;
use Qubus\Error\Handlers\Psr3ErrorHandler;
use Qubus\Error\Handlers\ProductionErrorHandler;

class Factory
{
    public static function createDebugErrorHandler(): DebugErrorHandler
    {
        return new DebugErrorHandler();
    }

    public static function createPsr3ErrorHandler(LoggerInterface $logger): Psr3ErrorHandler
    {
        return new Psr3ErrorHandler($logger);
    }

    public static function createProductionErrorHandler(
        bool $displayErrors = false,
        bool $notifyPsr3Loggers = false,
        bool $notifyNativePhpLog = true
    ): ProductionErrorHandler {
        return new ProductionErrorHandler($displayErrors, $notifyPsr3Loggers, $notifyNativePhpLog);
    }
}
