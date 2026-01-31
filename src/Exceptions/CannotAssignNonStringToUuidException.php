<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Exceptions;

use RuntimeException;

use function get_debug_type;
use function sprintf;

final class CannotAssignNonStringToUuidException extends RuntimeException
{
    public static function forValue(mixed $value): self
    {
        $type = get_debug_type($value);

        return new self(
            sprintf('Cannot assign non-string value of type [%s] to UUID primary key. ', $type).
            'UUID primary keys must be strings.',
        );
    }
}
