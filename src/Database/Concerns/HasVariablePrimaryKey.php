<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Database\Concerns;

use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Exceptions\CannotAssignNonStringToUlidException;
use Cline\VariableKeys\Exceptions\CannotAssignNonStringToUuidException;
use Cline\VariableKeys\Support\PrimaryKeyGenerator;
use Cline\VariableKeys\VariableKeysRegistry;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Model;

use function in_array;
use function is_string;
use function resolve;

/**
 * Configures primary key type based on runtime registration.
 *
 * This trait dynamically applies the appropriate primary key strategy (auto-increment,
 * ULID, or UUID) based on the model's registration with VariableKeysRegistry. Models
 * using this trait MUST be registered via VariableKeys::map() in a service provider.
 */
trait HasVariablePrimaryKey
{
    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * Returns false for ULID/UUID primary keys, true for auto-increment integers.
     * Overrides the model's default incrementing behavior based on the registered
     * primary key type.
     *
     * @return bool False if using ULID/UUID keys, true for auto-increment
     */
    public function getIncrementing(): bool
    {
        if (in_array($this->getKeyName(), $this->uniqueIds(), true)) {
            return false;
        }

        return $this->incrementing;
    }

    /**
     * Get the data type of the primary key.
     *
     * Returns 'string' for ULID/UUID keys, or the model's default key type
     * for auto-increment integers. Used by Eloquent for proper query parameter
     * binding and model serialization.
     *
     * @return string The PHP type of the primary key ('int' or 'string')
     */
    public function getKeyType(): string
    {
        if (in_array($this->getKeyName(), $this->uniqueIds(), true)) {
            return 'string';
        }

        return $this->keyType;
    }

    /**
     * Generate a new UUID/ULID for the model.
     *
     * Creates a unique identifier string based on the registered primary key type.
     * Returns null for auto-increment keys. Called automatically by Laravel's
     * HasUuids/HasUlids traits during model creation.
     *
     * @return null|string The generated unique ID, or null for auto-increment keys
     */
    public function newUniqueId(): ?string
    {
        $registry = resolve(VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(static::class);

        return PrimaryKeyGenerator::generate($primaryKeyType)->value;
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * Returns an array containing the primary key column name if using ULID/UUID,
     * or an empty array for auto-increment keys. Integrates with Laravel's unique
     * ID generation system for automatic key assignment during model creation.
     *
     * @return list<string> Array of column names requiring unique IDs, or empty for auto-increment
     */
    public function uniqueIds(): array
    {
        $registry = resolve(VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(static::class);

        return match ($primaryKeyType) {
            PrimaryKeyType::ULID, PrimaryKeyType::UUID => [$this->getKeyName()],
            PrimaryKeyType::ID => [],
        };
    }

    /**
     * Boot the HasVariablePrimaryKey trait.
     *
     * Registers a creating event listener that generates the primary key
     * when using ULID or UUID primary key types. Validates that existing
     * values are compatible with the configured key type, throwing exceptions
     * for type mismatches.
     */
    #[Boot()]
    protected static function initializePrimaryKeyGeneration(): void
    {
        static::creating(function (Model $model): void {
            $registry = resolve(VariableKeysRegistry::class);
            $primaryKeyType = $registry->getPrimaryKeyType($model::class);
            $primaryKey = PrimaryKeyGenerator::generate($primaryKeyType);

            if ($primaryKey->isAutoIncrementing()) {
                return;
            }

            $keyName = $model->getKeyName();
            $existingValue = $model->getAttribute($keyName);

            // Generate UUID/ULID if no value exists
            if (!$existingValue) {
                $model->setAttribute($keyName, $primaryKey->value);

                return;
            }

            // Validate that the existing value is compatible with the key type
            if ($primaryKey->type === PrimaryKeyType::UUID && !is_string($existingValue)) {
                throw CannotAssignNonStringToUuidException::forValue($existingValue);
            }

            if ($primaryKey->type === PrimaryKeyType::ULID && !is_string($existingValue)) {
                throw CannotAssignNonStringToUlidException::forValue($existingValue);
            }
        });
    }
}
