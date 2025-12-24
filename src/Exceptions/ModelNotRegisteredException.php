<?php

declare(strict_types=1);

namespace Cline\VariableKeys\Exceptions;

use RuntimeException;

final class ModelNotRegisteredException extends RuntimeException
{
    public static function make(string $model): self
    {
        return new self(
            "Model [{$model}] is not registered with VariableKeys. " .
            "Call VariableKeys::map() in your service provider to register this model."
        );
    }
}
