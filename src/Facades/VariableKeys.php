<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Facades;

use Cline\VariableKeys\Enums\MorphType;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\VariableKeysRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void           clear()
 * @method static MorphType      getMorphType(string $model)
 * @method static PrimaryKeyType getPrimaryKeyType(string $model)
 * @method static bool           isRegistered(string $model)
 * @method static void           map(array<class-string, array{primary_key_type: PrimaryKeyType, morph_type?: MorphType}> $mappings)
 *
 * @see VariableKeysRegistry
 */
final class VariableKeys extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return VariableKeysRegistry::class;
    }
}
