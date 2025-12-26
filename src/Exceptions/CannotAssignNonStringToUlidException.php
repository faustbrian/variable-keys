<?php

declare(strict_types=1);

namespace Cline\VariableKeys\Exceptions;

use RuntimeException;

final class CannotAssignNonStringToUlidException extends RuntimeException
{
    public static function forValue(mixed $value): self
    {
        $type = get_debug_type($value);

        return new self(
            "Cannot assign non-string value of type [{$type}] to ULID primary key. " .
            'ULID primary keys must be strings.'
        );
    }
}
