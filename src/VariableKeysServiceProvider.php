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
use Illuminate\Database\Schema\Blueprint;
use Override;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for the Variable Keys package.
 *
 * Registers Blueprint macros that eliminate repetitive match expressions
 * for primary keys, foreign keys, and polymorphic relationships.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class VariableKeysServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package settings.
     */
    public function configurePackage(Package $package): void
    {
        $package->name('variable-keys');
    }

    /**
     * Register Blueprint macros during package boot.
     */
    #[Override()]
    public function bootingPackage(): void
    {
        $this->registerVariablePrimaryKeyMacro();
        $this->registerVariableForeignKeyMacro();
        $this->registerVariableMorphsMacro();
    }

    /**
     * Register the variablePrimaryKey macro.
     *
     * Replaces verbose match expressions for primary key definitions:
     *
     * Before:
     * match ($primaryKeyType) {
     *     PrimaryKeyType::ULID => $table->ulid('id')->primary(),
     *     PrimaryKeyType::UUID => $table->uuid('id')->primary(),
     *     PrimaryKeyType::ID => $table->id(),
     * };
     *
     * After:
     * $table->variablePrimaryKey($primaryKeyType);
     */
    private function registerVariablePrimaryKeyMacro(): void
    {
        Blueprint::macro('variablePrimaryKey', function (PrimaryKeyType $type, string $column = 'id') {
            /** @var Blueprint $this */
            return match ($type) {
                PrimaryKeyType::ULID => $this->ulid($column)->primary(),
                PrimaryKeyType::UUID => $this->uuid($column)->primary(),
                PrimaryKeyType::ID => $this->id($column),
            };
        });
    }

    /**
     * Register the variableForeignKey macro.
     *
     * Replaces verbose match expressions for foreign key definitions:
     *
     * Before:
     * match ($primaryKeyType) {
     *     PrimaryKeyType::ULID => $table->foreignUlid('role_id'),
     *     PrimaryKeyType::UUID => $table->foreignUuid('role_id'),
     *     PrimaryKeyType::ID => $table->foreignId('role_id'),
     * };
     *
     * After:
     * $table->variableForeignKey('role_id', $primaryKeyType);
     */
    private function registerVariableForeignKeyMacro(): void
    {
        Blueprint::macro('variableForeignKey', function (string $column, PrimaryKeyType $type) {
            /** @var Blueprint $this */
            return match ($type) {
                PrimaryKeyType::ULID => $this->foreignUlid($column),
                PrimaryKeyType::UUID => $this->foreignUuid($column),
                PrimaryKeyType::ID => $this->foreignId($column),
            };
        });
    }

    /**
     * Register the variableMorphs macro.
     *
     * Replaces verbose match expressions for polymorphic relationship definitions:
     *
     * Before:
     * match ($morphType) {
     *     MorphType::ULID => $table->ulidMorphs('subject'),
     *     MorphType::UUID => $table->uuidMorphs('subject'),
     *     MorphType::Numeric => $table->numericMorphs('subject'),
     *     MorphType::String => $table->morphs('subject'),
     * };
     *
     * After:
     * $table->variableMorphs('subject', $morphType);
     */
    private function registerVariableMorphsMacro(): void
    {
        Blueprint::macro('variableMorphs', function (string $name, MorphType $type, bool $nullable = false) {
            /** @var Blueprint $this */
            if ($nullable) {
                return match ($type) {
                    MorphType::ULID => $this->nullableUlidMorphs($name),
                    MorphType::UUID => $this->nullableUuidMorphs($name),
                    MorphType::Numeric => $this->nullableNumericMorphs($name),
                    MorphType::String => $this->nullableMorphs($name),
                };
            }

            return match ($type) {
                MorphType::ULID => $this->ulidMorphs($name),
                MorphType::UUID => $this->uuidMorphs($name),
                MorphType::Numeric => $this->numericMorphs($name),
                MorphType::String => $this->morphs($name),
            };
        });
    }
}
