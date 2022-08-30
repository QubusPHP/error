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

namespace Qubus\Error\Exceptions;

use ErrorException;

class ContextErrorException extends ErrorException
{
    protected array $context = [];

    public function __construct(
        string $message,
        int $code,
        int $severity,
        ?string $filename = null,
        ?int $lineno = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $severity, $filename, $lineno);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
