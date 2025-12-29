<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys;

use Cline\VariableKeys\Enums\MorphType;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Exceptions\ModelNotRegisteredException;
use Illuminate\Container\Attributes\Singleton;

#[Singleton()]
final class VariableKeysRegistry
{
    /** @var array<class-string, array{primary_key_type: PrimaryKeyType, morph_type?: MorphType}> */
    private array $mappings = [];

    /**
     * Register model-to-configuration mappings.
     *
     * @param array<class-string, array{primary_key_type: PrimaryKeyType, morph_type?: MorphType}> $mappings
     */
    public function map(array $mappings): void
    {
        foreach ($mappings as $model => $config) {
            $this->mappings[$model] = $config;
        }
    }

    /**
     * Get the primary key type for a model.
     *
     * @param class-string $model
     *
     * @throws ModelNotRegisteredException
     */
    public function getPrimaryKeyType(string $model): PrimaryKeyType
    {
        if (!isset($this->mappings[$model]['primary_key_type'])) {
            throw ModelNotRegisteredException::make($model);
        }

        return $this->mappings[$model]['primary_key_type'];
    }

    /**
     * Get the morph type for a model.
     *
     * @param class-string $model
     *
     * @throws ModelNotRegisteredException
     */
    public function getMorphType(string $model): MorphType
    {
        if (!isset($this->mappings[$model]['morph_type'])) {
            throw ModelNotRegisteredException::make($model);
        }

        return $this->mappings[$model]['morph_type'];
    }

    /**
     * Check if a model is registered.
     *
     * @param class-string $model
     */
    public function isRegistered(string $model): bool
    {
        return isset($this->mappings[$model]);
    }

    /**
     * Clear all registered mappings (useful for testing).
     */
    public function clear(): void
    {
        $this->mappings = [];
    }
}
