<?php

declare(strict_types=1);

namespace Cline\VariableKeys\Exceptions;

use RuntimeException;

final class CannotAssignNonStringToUuidException extends RuntimeException
{
    public static function forValue(mixed $value): self
    {
        $type = get_debug_type($value);

        return new self(
            "Cannot assign non-string value of type [{$type}] to UUID primary key. " .
            'UUID primary keys must be strings.'
        );
    }
}
