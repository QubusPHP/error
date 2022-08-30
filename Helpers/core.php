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

namespace Qubus\Error\Helpers;

use Qubus\Error\Error;

/**
 * Check whether variable is a Qubus Error.
 *
 * Returns true if $object is an object of the `Error` class.
 *
 * @param mixed $object Check if unknown variable is an `Error` object.
 * @return bool True, if `Error`, false otherwise.
 */
function is_error(mixed $object): bool
{
    return ($object instanceof Error);
}
