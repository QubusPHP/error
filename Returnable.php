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

use Stringable;

interface Returnable extends Stringable
{
    public function getMessage(): string;
    public function getCode(): int;
    public function getContext(): array;
}
