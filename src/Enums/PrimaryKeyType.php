<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Enums;

/**
 * Defines supported primary key types for database migrations.
 *
 * Represents the available primary key generation strategies for Laravel migrations.
 * Each type corresponds to a different identifier format with distinct performance
 * characteristics, security implications, and use cases ranging from traditional
 * auto-incrementing integers to globally unique identifiers.
 *
 * Used with the variablePrimaryKey and variableForeignKey Blueprint macros to
 * dynamically create primary and foreign key columns based on your application's
 * requirements for uniqueness, performance, and security.
 *
 * @see \Cline\VariableKeys\VariableKeysServiceProvider::registerVariablePrimaryKeyMacro()
 * @see \Cline\VariableKeys\VariableKeysServiceProvider::registerVariableForeignKeyMacro()
 */
enum PrimaryKeyType: string
{
    /**
     * Traditional auto-incrementing integer primary keys.
     *
     * Standard sequential unsigned big integer IDs automatically incremented by
     * the database engine. Provides optimal query performance and minimal storage
     * overhead but reveals record count, creation ordering, and allows enumeration
     * attacks. Best suited for internal systems where ID predictability is acceptable.
     */
    case ID = 'id';

    /**
     * Universally Unique Lexicographically Sortable Identifiers.
     *
     * 26-character case-insensitive string identifiers combining a 48-bit timestamp
     * with 80 bits of randomness. Provides global uniqueness with time-ordered
     * sortability, enabling efficient database indexing through improved B-tree
     * locality. Offers better performance than UUIDs while preventing enumeration
     * attacks and maintaining chronological ordering for queries and pagination.
     */
    case ULID = 'ulid';

    /**
     * Universally Unique Identifiers (version 4).
     *
     * 36-character string identifiers (32 hexadecimal digits plus 4 hyphens) that
     * are globally unique and cryptographically random. Provides strong uniqueness
     * guarantees suitable for distributed systems and prevents enumeration attacks,
     * but lacks chronological ordering which can lead to fragmented database indexes
     * and reduced query performance compared to time-ordered alternatives like ULIDs.
     */
    case UUID = 'uuid';
}
