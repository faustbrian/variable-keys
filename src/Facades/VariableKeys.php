<?php

declare(strict_types=1);

namespace Cline\VariableKeys\Facades;

use Cline\VariableKeys\Enums\MorphType;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\VariableKeysRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void map(array $mappings)
 * @method static PrimaryKeyType getPrimaryKeyType(string $model)
 * @method static MorphType getMorphType(string $model)
 * @method static bool isRegistered(string $model)
 * @method static void clear()
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
