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
 * Registers Laravel Blueprint macros that eliminate repetitive match expressions
 * when defining primary keys, foreign keys, and polymorphic relationships with
 * variable identifier types. Provides three convenience macros: variablePrimaryKey,
 * variableForeignKey, and variableMorphs, which accept enum type parameters to
 * dynamically generate the appropriate column definitions.
 *
 * Simplifies migration code by replacing verbose match statements with concise
 * macro calls, improving readability and maintainability when working with
 * applications that support multiple primary key strategies.
 *
 * @package Cline\VariableKeys
 *
 * @see \Cline\VariableKeys\Enums\PrimaryKeyType
 * @see \Cline\VariableKeys\Enums\MorphType
 */
final class VariableKeysServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package settings.
     *
     * Sets the package identifier used by Spatie's Laravel Package Tools.
     * This name is used for configuration file publishing, command registration,
     * and other package-related functionality.
     *
     * @param Package $package The package configuration instance to configure
     *
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package->name('variable-keys');
    }

    /**
     * Register Blueprint macros during package boot.
     *
     * Invoked automatically during the Laravel service provider boot process.
     * Registers three Blueprint macros that extend Laravel's schema builder
     * with dynamic column creation methods based on identifier type enums.
     *
     * @return void
     */
    #[Override()]
    public function bootingPackage(): void
    {
        $this->registerVariablePrimaryKeyMacro();
        $this->registerVariableForeignKeyMacro();
        $this->registerVariableMorphsMacro();
    }

    /**
     * Register the variablePrimaryKey Blueprint macro.
     *
     * Extends Blueprint with a variablePrimaryKey method that dynamically creates
     * primary key columns based on the provided PrimaryKeyType enum. Eliminates
     * repetitive match expressions and centralizes primary key creation logic.
     *
     * ```php
     * // Before - verbose match expression
     * match ($primaryKeyType) {
     *     PrimaryKeyType::ULID => $table->ulid('id')->primary(),
     *     PrimaryKeyType::UUID => $table->uuid('id')->primary(),
     *     PrimaryKeyType::ID => $table->id(),
     * };
     *
     * // After - concise macro call
     * $table->variablePrimaryKey($primaryKeyType);
     * ```
     *
     * @return void
     */
    private function registerVariablePrimaryKeyMacro(): void
    {
        Blueprint::macro(
            'variablePrimaryKey',
            /**
             * Create a primary key column with variable identifier type.
             *
             * @param PrimaryKeyType $type   The primary key type (ID, ULID, or UUID) that determines
             *                               the column type and characteristics for the primary key
             * @param string         $column The column name for the primary key, defaults to 'id'
             *                               following Laravel naming conventions
             *
             * @return \Illuminate\Database\Schema\ColumnDefinition
             */
            function (PrimaryKeyType $type, string $column = 'id') {
                /** @var Blueprint $this */
                return match ($type) {
                    PrimaryKeyType::ULID => $this->ulid($column)->primary(),
                    PrimaryKeyType::UUID => $this->uuid($column)->primary(),
                    PrimaryKeyType::ID => $this->id($column),
                };
            }
        );
    }

    /**
     * Register the variableForeignKey Blueprint macro.
     *
     * Extends Blueprint with a variableForeignKey method that dynamically creates
     * foreign key columns based on the provided PrimaryKeyType enum. Ensures foreign
     * key column types match the referenced table's primary key type, eliminating
     * type mismatch errors and repetitive match expressions.
     *
     * ```php
     * // Before - verbose match expression
     * match ($primaryKeyType) {
     *     PrimaryKeyType::ULID => $table->foreignUlid('role_id'),
     *     PrimaryKeyType::UUID => $table->foreignUuid('role_id'),
     *     PrimaryKeyType::ID => $table->foreignId('role_id'),
     * };
     *
     * // After - concise macro call
     * $table->variableForeignKey('role_id', $primaryKeyType);
     * ```
     *
     * @return void
     */
    private function registerVariableForeignKeyMacro(): void
    {
        Blueprint::macro(
            'variableForeignKey',
            /**
             * Create a foreign key column with variable identifier type.
             *
             * @param string         $column The column name for the foreign key, typically following
             *                               the naming convention of {related_table}_id
             * @param PrimaryKeyType $type   The primary key type (ID, ULID, or UUID) that must match
             *                               the referenced table's primary key type to ensure proper
             *                               foreign key constraint creation and query performance
             *
             * @return \Illuminate\Database\Schema\ForeignIdColumnDefinition|\Illuminate\Database\Schema\ColumnDefinition
             */
            function (string $column, PrimaryKeyType $type) {
                /** @var Blueprint $this */
                return match ($type) {
                    PrimaryKeyType::ULID => $this->foreignUlid($column),
                    PrimaryKeyType::UUID => $this->foreignUuid($column),
                    PrimaryKeyType::ID => $this->foreignId($column),
                };
            }
        );
    }

    /**
     * Register the variableMorphs Blueprint macro.
     *
     * Extends Blueprint with a variableMorphs method that dynamically creates
     * polymorphic relationship columns based on the provided MorphType enum.
     * Generates both the {name}_type and {name}_id columns required for Laravel's
     * polymorphic relationships, with support for nullable relationships.
     *
     * ```php
     * // Before - verbose match expression
     * match ($morphType) {
     *     MorphType::ULID => $table->ulidMorphs('subject'),
     *     MorphType::UUID => $table->uuidMorphs('subject'),
     *     MorphType::Numeric => $table->numericMorphs('subject'),
     *     MorphType::String => $table->morphs('subject'),
     * };
     *
     * // After - concise macro call
     * $table->variableMorphs('subject', $morphType);
     * $table->variableMorphs('subject', $morphType, nullable: true);
     * ```
     *
     * @return void
     */
    private function registerVariableMorphsMacro(): void
    {
        Blueprint::macro(
            'variableMorphs',
            /**
             * Create polymorphic relationship columns with variable identifier type.
             *
             * Creates both the type column ({name}_type) and ID column ({name}_id) required
             * for Laravel polymorphic relationships, with the ID column type determined by
             * the provided MorphType enum.
             *
             * @param string    $name     The base name for the polymorphic relationship, used to
             *                            generate the {name}_type and {name}_id column names
             * @param MorphType $type     The morph type (String, Numeric, UUID, or ULID) that
             *                            determines the ID column type for the polymorphic relationship
             * @param bool      $nullable Whether the polymorphic relationship columns should allow NULL
             *                            values, enabling optional polymorphic relationships. Defaults
             *                            to false for required relationships
             *
             * @return void
             */
            function (string $name, MorphType $type, bool $nullable = false) {
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
            }
        );
    }
}
