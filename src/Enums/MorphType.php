<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Enums;

/**
 * Defines supported morph types for polymorphic relationships.
 *
 * Represents the available polymorphic relationship types for Laravel migrations.
 * Each type corresponds to a different identifier format optimized for specific
 * use cases, from traditional auto-incrementing IDs to globally unique identifiers.
 *
 * Used with the variableMorphs Blueprint macro to dynamically create polymorphic
 * relationship columns based on your application's primary key strategy.
 *
 * @package Cline\VariableKeys
 *
 * @see \Cline\VariableKeys\VariableKeysServiceProvider::registerVariableMorphsMacro()
 */
enum MorphType: string
{
    /**
     * Standard polymorphic relationship with auto-detected IDs.
     *
     * Uses Laravel's default morphs() method which automatically detects
     * the appropriate column type based on the related model's primary key.
     * Provides flexibility when polymorphic relationships may point to models
     * with varying primary key types.
     *
     * @var string
     */
    case String = 'string';

    /**
     * Polymorphic relationship with numeric (integer) IDs.
     *
     * Explicitly uses unsigned big integer foreign keys for the morph relationship.
     * Optimized for models using standard auto-incrementing integer primary keys,
     * providing better performance and smaller index sizes compared to UUID/ULID
     * alternatives when global uniqueness is not required.
     *
     * @var string
     */
    case Numeric = 'numeric';

    /**
     * Polymorphic relationship with UUID identifiers.
     *
     * Uses 36-character UUID strings (32 hex digits plus 4 hyphens) for the morph
     * relationship foreign key. Provides globally unique, cryptographically random
     * identifiers suitable when models use UUID primary keys and distributed systems
     * require guaranteed uniqueness across multiple databases or servers.
     *
     * @var string
     */
    case UUID = 'uuid';

    /**
     * Polymorphic relationship with ULID identifiers.
     *
     * Uses 26-character ULID strings (case-insensitive) for the morph relationship
     * foreign key. Combines the benefits of UUIDs (global uniqueness) with
     * time-ordered sortability, providing better database performance through
     * improved B-tree index efficiency and chronological ordering capabilities.
     *
     * @var string
     */
    case ULID = 'ulid';
}
