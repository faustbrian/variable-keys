<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Exceptions;

use RuntimeException;

use function sprintf;

final class ModelNotRegisteredException extends RuntimeException
{
    public static function make(string $model): self
    {
        return new self(
            sprintf('Model [%s] is not registered with VariableKeys. ', $model).
            'Call VariableKeys::map() in your service provider to register this model.',
        );
    }
}
