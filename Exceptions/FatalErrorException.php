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

namespace Qubus\Error\Exceptions;

use ErrorException;

class FatalErrorException extends ErrorException
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
        parent::__construct(message: $message, code: $code, severity: $severity, filename: $filename, line: $lineno);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
