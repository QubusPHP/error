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

namespace Qubus\Error;

class Error implements Returnable
{
    public function __construct(
        protected string $message = '',
        protected int|string $code = '',
        protected $context = []
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function __toString(): string
    {
        return sprintf('%s:: %s', (string) $this->code, $this->message);
    }
}
