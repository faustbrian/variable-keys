## Table of Contents

1. Overview (`docs/README.md`)
2. Api Reference (`docs/api-reference.md`)
3. Configuration Patterns (`docs/configuration-patterns.md`)
4. Foreign Keys (`docs/foreign-keys.md`)
5. Polymorphic Relations (`docs/polymorphic-relations.md`)
6. Primary Keys (`docs/primary-keys.md`)
Eliminate repetitive match expressions in your Laravel migrations with type-safe Blueprint macros for variable primary keys, foreign keys, and polymorphic relationships.

## Requirements

Variable Keys v1.0 requires PHP 8.5+ and Laravel 12+.

## Installation

Install Variable Keys with composer:

```bash
composer require cline/variable-keys
```

The package will automatically register its service provider and Blueprint macros.

## Quick Example

Instead of writing verbose match expressions in migrations:

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;

Schema::create('users', function (Blueprint $table) {
    // Before: verbose match expression
    match (PrimaryKeyType::ULID) {
        PrimaryKeyType::ULID => $table->ulid('id')->primary(),
        PrimaryKeyType::UUID => $table->uuid('id')->primary(),
        PrimaryKeyType::ID => $table->id(),
    };

    $table->string('name');
    $table->timestamps();
});
```

Use clean, type-safe macros:

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;

Schema::create('users', function (Blueprint $table) {
    // After: clean macro
    $table->variablePrimaryKey(PrimaryKeyType::ULID);

    $table->string('name');
    $table->timestamps();
});
```

## Model Integration

Register models in your service provider to enable automatic primary key generation:

```php
use Cline\VariableKeys\Facades\VariableKeys;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        VariableKeys::map([
            User::class => [
                'primary_key_type' => PrimaryKeyType::ULID,
            ],
        ]);
    }
}
```

Add the trait to your models:

```php
use Cline\VariableKeys\Database\Concerns\HasVariablePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasVariablePrimaryKey;
}

## What's Included

Variable Keys provides three Blueprint macros and two enums:

### Enums

- **`PrimaryKeyType`** - ID, ULID, UUID
- **`MorphType`** - String, Numeric, UUID, ULID

### Blueprint Macros

- **`variablePrimaryKey()`** - Primary key columns
- **`variableForeignKey()`** - Foreign key columns
- **`variableMorphs()`** - Polymorphic relationship columns

## Next Steps

- [Primary Keys](primary-keys.md) - Configure variable primary keys
- [Foreign Keys](foreign-keys.md) - Manage foreign key relationships
- [Polymorphic Relations](polymorphic-relations.md) - Handle polymorphic relationships
- [Configuration Patterns](configuration-patterns.md) - Centralize key type configuration

Complete API documentation for Variable Keys enums and Blueprint macros.

## Enums

### PrimaryKeyType

Represents the available primary key generation strategies for Laravel migrations.

**Namespace:** `Cline\VariableKeys\Enums\PrimaryKeyType`

#### Cases

##### ID

```php
PrimaryKeyType::ID
```

Traditional auto-incrementing integer primary keys. Simplest option but reveals record count and ordering.

**String value:** `'id'`

##### ULID

```php
PrimaryKeyType::ULID
```

Universally Unique Lexicographically Sortable Identifiers. 26-character case-insensitive strings that are time-ordered and globally unique.

**String value:** `'ulid'`

##### UUID

```php
PrimaryKeyType::UUID
```

Universally Unique Identifiers (version 4). 36-character strings (32 hex digits plus 4 hyphens) that are globally unique and cryptographically random.

**String value:** `'uuid'`

#### Usage

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;

// Direct usage
$type = PrimaryKeyType::ULID;

// From string value
$type = PrimaryKeyType::tryFrom('ulid') ?? PrimaryKeyType::ID;

// From configuration
$type = PrimaryKeyType::tryFrom(config('database.primary_key_type'))
    ?? PrimaryKeyType::ID;
```

---

### MorphType

Represents the available polymorphic relationship types for Laravel migrations.

**Namespace:** `Cline\VariableKeys\Enums\MorphType`

#### Cases

##### String

```php
MorphType::String
```

Standard polymorphic relationship with auto-detected IDs. Uses Laravel's default morphs() method.

**String value:** `'string'`

##### Numeric

```php
MorphType::Numeric
```

Polymorphic relationship with numeric (integer) IDs. Suitable for auto-incrementing integer primary keys.

**String value:** `'numeric'`

##### UUID

```php
MorphType::UUID
```

Polymorphic relationship with UUID identifiers. Uses 36-character UUIDs.

**String value:** `'uuid'`

##### ULID

```php
MorphType::ULID
```

Polymorphic relationship with ULID identifiers. Uses 26-character ULIDs.

**String value:** `'ulid'`

#### Usage

```php
use Cline\VariableKeys\Enums\MorphType;

// Direct usage
$type = MorphType::ULID;

// From string value
$type = MorphType::tryFrom('ulid') ?? MorphType::String;

// From configuration
$type = MorphType::tryFrom(config('database.morph_type'))
    ?? MorphType::String;
```

---

## Blueprint Macros

All macros are automatically registered when the package is installed. They extend Laravel's `Illuminate\Database\Schema\Blueprint` class.

### variablePrimaryKey()

Creates a primary key column based on the specified type.

#### Signature

```php
public function variablePrimaryKey(
    PrimaryKeyType $type,
    string $column = 'id'
): \Illuminate\Database\Schema\ColumnDefinition
```

#### Parameters

**`$type`** (PrimaryKeyType, required)
- The primary key type to use
- Must be one of: `PrimaryKeyType::ID`, `PrimaryKeyType::ULID`, or `PrimaryKeyType::UUID`

**`$column`** (string, optional)
- The column name for the primary key
- Default: `'id'`

#### Returns

`ColumnDefinition` - A column definition instance for the created primary key

#### Examples

```php
// Auto-incrementing integer ID
$table->variablePrimaryKey(PrimaryKeyType::ID);

// ULID primary key
$table->variablePrimaryKey(PrimaryKeyType::ULID);

// UUID primary key
$table->variablePrimaryKey(PrimaryKeyType::UUID);

// Custom column name
$table->variablePrimaryKey(PrimaryKeyType::ULID, 'user_id');
```

#### Equivalent Laravel Methods

```php
// PrimaryKeyType::ID
$table->id();
$table->id('user_id');

// PrimaryKeyType::ULID
$table->ulid('id')->primary();
$table->ulid('user_id')->primary();

// PrimaryKeyType::UUID
$table->uuid('id')->primary();
$table->uuid('user_id')->primary();
```

---

### variableForeignKey()

Creates a foreign key column that matches the specified primary key type.

#### Signature

```php
public function variableForeignKey(
    string $column,
    PrimaryKeyType $type
): \Illuminate\Database\Schema\ForeignKeyDefinition
```

#### Parameters

**`$column`** (string, required)
- The foreign key column name
- Example: `'user_id'`, `'category_id'`, `'parent_id'`

**`$type`** (PrimaryKeyType, required)
- The primary key type to match
- Must be one of: `PrimaryKeyType::ID`, `PrimaryKeyType::ULID`, or `PrimaryKeyType::UUID`

#### Returns

`ForeignKeyDefinition` - A foreign key definition instance for chaining constraints

#### Examples

```php
// Integer foreign key
$table->variableForeignKey('user_id', PrimaryKeyType::ID)
      ->constrained()
      ->cascadeOnDelete();

// ULID foreign key
$table->variableForeignKey('author_id', PrimaryKeyType::ULID)
      ->constrained('users', 'id');

// UUID foreign key with nullable
$table->variableForeignKey('parent_id', PrimaryKeyType::UUID)
      ->nullable()
      ->constrained('posts')
      ->nullOnDelete();
```

#### Chainable Methods

The returned `ForeignKeyDefinition` supports all standard Laravel foreign key methods:

```php
->constrained(?string $table = null, ?string $column = 'id')
->cascadeOnUpdate()
->restrictOnUpdate()
->cascadeOnDelete()
->restrictOnDelete()
->nullOnDelete()
->noActionOnDelete()
->index()
->nullable()
```

#### Equivalent Laravel Methods

```php
// PrimaryKeyType::ID
$table->foreignId('user_id');

// PrimaryKeyType::ULID
$table->foreignUlid('user_id');

// PrimaryKeyType::UUID
$table->foreignUuid('user_id');
```

---

### variableMorphs()

Creates polymorphic relationship columns (type and ID) based on the specified morph type.

#### Signature

```php
public function variableMorphs(
    string $name,
    MorphType $type,
    bool $nullable = false
): void
```

#### Parameters

**`$name`** (string, required)
- The morph relationship name
- Creates `{name}_type` and `{name}_id` columns
- Example: `'commentable'` creates `commentable_type` and `commentable_id`

**`$type`** (MorphType, required)
- The morph type to use
- Must be one of: `MorphType::String`, `MorphType::Numeric`, `MorphType::UUID`, or `MorphType::ULID`

**`$nullable`** (bool, optional)
- Whether the relationship columns should be nullable
- Default: `false`
- When `true`, uses Laravel's `nullableXxxMorphs()` methods

#### Returns

`void` - Creates the morph columns but returns nothing

#### Examples

```php
// String morphs (auto-detected)
$table->variableMorphs('commentable', MorphType::String);
// Creates: commentable_type (string), commentable_id (unsignedBigInteger)

// Numeric morphs
$table->variableMorphs('taggable', MorphType::Numeric);
// Creates: taggable_type (string), taggable_id (unsignedBigInteger)

// UUID morphs
$table->variableMorphs('imageable', MorphType::UUID);
// Creates: imageable_type (string), imageable_id (char(36))

// ULID morphs
$table->variableMorphs('attachable', MorphType::ULID);
// Creates: attachable_type (string), attachable_id (char(26))

// Nullable relationship
$table->variableMorphs('parent', MorphType::ULID, nullable: true);
// Creates nullable versions of both columns
```

#### Equivalent Laravel Methods

```php
// MorphType::String
$table->morphs('commentable');
$table->nullableMorphs('commentable'); // when nullable: true

// MorphType::Numeric
$table->numericMorphs('commentable');
$table->nullableNumericMorphs('commentable'); // when nullable: true

// MorphType::UUID
$table->uuidMorphs('commentable');
$table->nullableUuidMorphs('commentable'); // when nullable: true

// MorphType::ULID
$table->ulidMorphs('commentable');
$table->nullableUlidMorphs('commentable'); // when nullable: true
```

---

## Type Conversion

### From String to Enum

Both enums support `tryFrom()` for safe conversion from string values:

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Enums\MorphType;

// Returns enum case or null
$primaryKey = PrimaryKeyType::tryFrom('ulid');
// Result: PrimaryKeyType::ULID

$morph = MorphType::tryFrom('invalid');
// Result: null

// With fallback
$primaryKey = PrimaryKeyType::tryFrom($value) ?? PrimaryKeyType::ID;
$morph = MorphType::tryFrom($value) ?? MorphType::String;
```

### Enum to String

Access the backing string value:

```php
$type = PrimaryKeyType::ULID;
echo $type->value; // "ulid"

$morph = MorphType::UUID;
echo $morph->value; // "uuid"
```

---

## Service Provider

The package automatically registers its service provider when installed via composer.

**Class:** `Cline\VariableKeys\VariableKeysServiceProvider`

**Namespace:** `Cline\VariableKeys`

### Registered Macros

The service provider registers the following Blueprint macros during boot:

1. `variablePrimaryKey()`
2. `variableForeignKey()`
3. `variableMorphs()`

No manual registration is required.

Learn how to configure and manage variable key types using runtime registration for type safety and explicit model configuration.

## Runtime Registration

Variable Keys uses **strict runtime registration** via the `VariableKeys` facade. Every model using the `HasVariablePrimaryKey` trait **must** be explicitly registered.

### Basic Registration

Register models in your service provider's `boot()` method:

```php
use Cline\VariableKeys\Facades\VariableKeys;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use App\Models\{User, Post, Comment};

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        VariableKeys::map([
            User::class => [
                'primary_key_type' => PrimaryKeyType::ULID,
            ],
            Post::class => [
                'primary_key_type' => PrimaryKeyType::ULID,
            ],
            Comment::class => [
                'primary_key_type' => PrimaryKeyType::ID,
            ],
        ]);
    }
}
```

### Type Safety

Registration requires **enum instances**, not strings:

```php
// ✅ Correct - uses enum
VariableKeys::map([
    User::class => ['primary_key_type' => PrimaryKeyType::ULID],
]);

// ❌ Wrong - strings not allowed
VariableKeys::map([
    User::class => ['primary_key_type' => 'ulid'], // Type error
]);
```

### Strict Validation

Models using the trait **must** be registered or an exception is thrown:

```php
use Cline\VariableKeys\Database\Concerns\HasVariablePrimaryKey;

class User extends Model
{
    use HasVariablePrimaryKey; // Must be registered
}

// If not registered:
$user = new User();
// Throws: ModelNotRegisteredException
```

## Package-Specific Registration

Packages should register their models in their own service providers:

```php
namespace Vendor\Package;

use Cline\VariableKeys\Facades\VariableKeys;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Vendor\Package\Models\{Ability, Role};

class PackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register package models
        VariableKeys::map([
            Ability::class => [
                'primary_key_type' => PrimaryKeyType::from(
                    config('package.primary_key_type', 'id')
                ),
            ],
            Role::class => [
                'primary_key_type' => PrimaryKeyType::from(
                    config('package.primary_key_type', 'id')
                ),
            ],
        ]);
    }
}
```

## Environment-Based Configuration

Use environment variables with enum conversion:

**.env**
```env
APP_PRIMARY_KEY_TYPE=ulid
```

**Service Provider**
```php
use Cline\VariableKeys\Enums\PrimaryKeyType;

VariableKeys::map([
    User::class => [
        'primary_key_type' => PrimaryKeyType::from(
            env('APP_PRIMARY_KEY_TYPE', 'id')
        ),
    ],
]);
```

### Per-Model Environment Variables

```env
USER_PRIMARY_KEY_TYPE=ulid
ORGANIZATION_PRIMARY_KEY_TYPE=uuid
POST_PRIMARY_KEY_TYPE=id
```

```php
VariableKeys::map([
    User::class => [
        'primary_key_type' => PrimaryKeyType::from(
            env('USER_PRIMARY_KEY_TYPE', 'id')
        ),
    ],
    Organization::class => [
        'primary_key_type' => PrimaryKeyType::from(
            env('ORGANIZATION_PRIMARY_KEY_TYPE', 'id')
        ),
    ],
    Post::class => [
        'primary_key_type' => PrimaryKeyType::from(
            env('POST_PRIMARY_KEY_TYPE', 'id')
        ),
    ],
]);
```

## Configuration Helpers

Create helper methods for consistent configuration:

```php
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerVariableKeys();
    }

    protected function registerVariableKeys(): void
    {
        VariableKeys::map([
            User::class => $this->keyConfig('USER'),
            Organization::class => $this->keyConfig('ORGANIZATION'),
            Post::class => $this->keyConfig('POST'),
        ]);
    }

    protected function keyConfig(string $prefix): array
    {
        return [
            'primary_key_type' => PrimaryKeyType::from(
                env("{$prefix}_PRIMARY_KEY_TYPE", 'id')
            ),
        ];
    }
}
```

## Multi-Tenancy Patterns

### Tenant-Specific Key Types

```php
use Cline\VariableKeys\Facades\VariableKeys;
use Cline\VariableKeys\Enums\PrimaryKeyType;

class TenantServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Global models use ULIDs
        VariableKeys::map([
            Tenant::class => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);

        // Tenant-scoped models use auto-increment
        if (tenancy()->initialized) {
            VariableKeys::map([
                Post::class => ['primary_key_type' => PrimaryKeyType::ID],
                Comment::class => ['primary_key_type' => PrimaryKeyType::ID],
            ]);
        }
    }
}
```

## Polymorphic Configuration

Register models with morph types for polymorphic relationships:

```php
use Cline\VariableKeys\Enums\{PrimaryKeyType, MorphType};

VariableKeys::map([
    User::class => [
        'primary_key_type' => PrimaryKeyType::ULID,
        'morph_type' => MorphType::ULID,
    ],
    Organization::class => [
        'primary_key_type' => PrimaryKeyType::UUID,
        'morph_type' => MorphType::UUID,
    ],
]);
```

## Testing Configuration

### Test-Specific Registration

Override registration in tests:

```php
use Cline\VariableKeys\Facades\VariableKeys;
use Cline\VariableKeys\Enums\PrimaryKeyType;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Use auto-increment in tests for simplicity
        VariableKeys::clear();
        VariableKeys::map([
            User::class => ['primary_key_type' => PrimaryKeyType::ID],
        ]);
    }
}
```

### Feature Flags

```php
VariableKeys::map([
    User::class => [
        'primary_key_type' => Feature::active('use-ulids')
            ? PrimaryKeyType::ULID
            : PrimaryKeyType::ID,
    ],
]);
```

## Migration Consistency

Ensure migrations match model registration:

```php
// Migration
use Cline\VariableKeys\Enums\PrimaryKeyType;

Schema::create('users', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::ULID);
    $table->string('name');
    $table->timestamps();
});

// Model registration (must match)
VariableKeys::map([
    User::class => ['primary_key_type' => PrimaryKeyType::ULID],
]);
```

## Best Practices

### 1. Register in Service Providers

Always register in service provider `boot()` method, never in models:

```php
// ✅ Good
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        VariableKeys::map([...]);
    }
}

// ❌ Bad - never in models
class User extends Model
{
    public function __construct()
    {
        VariableKeys::map([...]); // Don't do this
    }
}
```

### 2. Explicit Over Implicit

Explicitly register every model - no fallbacks or wildcards:

```php
// ✅ Good - explicit registration
VariableKeys::map([
    User::class => ['primary_key_type' => PrimaryKeyType::ULID],
    Post::class => ['primary_key_type' => PrimaryKeyType::ULID],
    Comment::class => ['primary_key_type' => PrimaryKeyType::ULID],
]);

// ❌ Bad - no wildcard support
VariableKeys::map([
    '*' => ['primary_key_type' => PrimaryKeyType::ULID], // Not supported
]);
```

### 3. Consistency Across Models

Use the same key type across related models:

```php
$keyType = PrimaryKeyType::ULID;

VariableKeys::map([
    User::class => ['primary_key_type' => $keyType],
    Post::class => ['primary_key_type' => $keyType],
    Comment::class => ['primary_key_type' => $keyType],
]);
```

### 4. Document Decisions

```php
/**
 * Primary Key Strategy: ULID
 *
 * Using ULIDs for:
 * - Distributed database support
 * - Time-ordered queries
 * - URL-safe identifiers
 * - No enumeration attacks
 */
VariableKeys::map([
    User::class => ['primary_key_type' => PrimaryKeyType::ULID],
]);
```

The `variableForeignKey()` macro creates foreign key columns that match your primary key type, eliminating the need for verbose match expressions.

## Basic Usage

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::create('posts', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::ULID);
    $table->string('title');

    // Foreign key automatically matches ULID type
    $table->variableForeignKey('user_id', PrimaryKeyType::ULID)
          ->constrained('users')
          ->cascadeOnDelete();

    $table->timestamps();
});
```

## Method Signature

```php
$table->variableForeignKey(string $column, PrimaryKeyType $type)
```

**Parameters:**
- `$column` - The foreign key column name (e.g., `'user_id'`, `'category_id'`)
- `$type` - The primary key type enum value

**Returns:** A `ForeignKeyDefinition` instance for chaining constraints.

## Type Matching

The macro automatically selects the correct foreign key method based on the type:

| PrimaryKeyType | Foreign Key Method |
|----------------|-------------------|
| `ID` | `foreignId()` |
| `ULID` | `foreignUlid()` |
| `UUID` | `foreignUuid()` |

## Chaining Constraints

The macro returns a foreign key definition, allowing you to chain Laravel's standard constraint methods:

```php
$table->variableForeignKey('user_id', PrimaryKeyType::UUID)
      ->constrained('users')
      ->cascadeOnUpdate()
      ->cascadeOnDelete();
```

### Common Constraints

```php
// Reference specific table and column
$table->variableForeignKey('author_id', PrimaryKeyType::ULID)
      ->constrained('users', 'id');

// Cascade on delete
$table->variableForeignKey('category_id', PrimaryKeyType::ID)
      ->constrained()
      ->cascadeOnDelete();

// Set null on delete
$table->variableForeignKey('parent_id', PrimaryKeyType::UUID)
      ->nullable()
      ->constrained('posts')
      ->nullOnDelete();

// Restrict deletion
$table->variableForeignKey('organization_id', PrimaryKeyType::ULID)
      ->constrained()
      ->restrictOnDelete();

// Add index
$table->variableForeignKey('team_id', PrimaryKeyType::ID)
      ->constrained()
      ->index();
```

## Configuration-Driven Foreign Keys

Centralize your primary key type to ensure consistency:

```php
$primaryKeyType = PrimaryKeyType::tryFrom(config('database.primary_key_type'))
    ?? PrimaryKeyType::ID;

Schema::create('comments', function (Blueprint $table) use ($primaryKeyType) {
    $table->variablePrimaryKey($primaryKeyType);
    $table->text('body');

    $table->variableForeignKey('post_id', $primaryKeyType)
          ->constrained()
          ->cascadeOnDelete();

    $table->variableForeignKey('user_id', $primaryKeyType)
          ->constrained('users');

    $table->timestamps();
});
```

## Before and After

### Before: Verbose Match Expression

```php
match ($primaryKeyType) {
    PrimaryKeyType::ULID => $table->foreignUlid('user_id')
                                  ->constrained('users')
                                  ->cascadeOnDelete(),
    PrimaryKeyType::UUID => $table->foreignUuid('user_id')
                                  ->constrained('users')
                                  ->cascadeOnDelete(),
    PrimaryKeyType::ID => $table->foreignId('user_id')
                                ->constrained('users')
                                ->cascadeOnDelete(),
};
```

### After: Clean Macro

```php
$table->variableForeignKey('user_id', $primaryKeyType)
      ->constrained('users')
      ->cascadeOnDelete();
```

## Examples

### Blog System with ULIDs

```php
$primaryKeyType = PrimaryKeyType::ULID;

Schema::create('posts', function (Blueprint $table) use ($primaryKeyType) {
    $table->variablePrimaryKey($primaryKeyType);
    $table->string('title');
    $table->text('body');

    $table->variableForeignKey('author_id', $primaryKeyType)
          ->constrained('users', 'id')
          ->cascadeOnDelete();

    $table->variableForeignKey('category_id', $primaryKeyType)
          ->constrained('categories')
          ->restrictOnDelete();

    $table->timestamps();
});
```

### Multi-Tenancy with UUIDs

```php
$primaryKeyType = PrimaryKeyType::UUID;

Schema::create('projects', function (Blueprint $table) use ($primaryKeyType) {
    $table->variablePrimaryKey($primaryKeyType);
    $table->string('name');

    $table->variableForeignKey('tenant_id', $primaryKeyType)
          ->constrained('tenants')
          ->restrictOnDelete();

    $table->variableForeignKey('created_by', $primaryKeyType)
          ->constrained('users', 'id');

    $table->timestamps();
});
```

### Self-Referential Relationships

```php
Schema::create('categories', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::ID);
    $table->string('name');

    $table->variableForeignKey('parent_id', PrimaryKeyType::ID)
          ->nullable()
          ->constrained('categories')
          ->cascadeOnDelete();

    $table->timestamps();
});
```

The `variableMorphs()` macro eliminates verbose match expressions when defining polymorphic relationship columns, making your migrations cleaner and type-safe.

## Basic Usage

```php
use Cline\VariableKeys\Enums\MorphType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->text('body');

    // Polymorphic relationship
    $table->variableMorphs('commentable', MorphType::ULID);

    $table->timestamps();
});
```

## Method Signature

```php
$table->variableMorphs(string $name, MorphType $type, bool $nullable = false)
```

**Parameters:**
- `$name` - The morph relationship name (e.g., `'commentable'`, `'taggable'`)
- `$type` - The morph type enum value
- `$nullable` - Whether the relationship is optional (default: `false`)

## Available Morph Types

### String (Auto-Detected)

Laravel's default morphs that automatically detect the appropriate column type.

```php
$table->variableMorphs('commentable', MorphType::String);
```

**Equivalent to:**
```php
$table->morphs('commentable');
```

### Numeric (Integer IDs)

Explicitly use integer foreign keys for the morph relationship. Best when models use auto-incrementing integer primary keys.

```php
$table->variableMorphs('taggable', MorphType::Numeric);
```

**Equivalent to:**
```php
$table->numericMorphs('taggable');
```

### UUID

Use 36-character UUIDs for the morph relationship.

```php
$table->variableMorphs('imageable', MorphType::UUID);
```

**Equivalent to:**
```php
$table->uuidMorphs('imageable');
```

### ULID

Use 26-character ULIDs for the morph relationship.

```php
$table->variableMorphs('attachable', MorphType::ULID);
```

**Equivalent to:**
```php
$table->ulidMorphs('attachable');
```

## Nullable Relationships

Set the third parameter to `true` for optional polymorphic relationships:

```php
$table->variableMorphs('parent', MorphType::UUID, nullable: true);
```

This creates nullable columns for both the type and ID:

```php
// Equivalent to:
$table->nullableUuidMorphs('parent');
```

## Configuration-Driven Morphs

Centralize your morph type configuration:

```php
// config/database.php
return [
    'morph_type' => env('DB_MORPH_TYPE', 'string'),
];
```

Use in migrations:

```php
use Cline\VariableKeys\Enums\MorphType;

$morphType = MorphType::tryFrom(config('database.morph_type'))
    ?? MorphType::String;

Schema::create('images', function (Blueprint $table) use ($morphType) {
    $table->id();
    $table->string('url');

    $table->variableMorphs('imageable', $morphType);

    $table->timestamps();
});
```

## Before and After

### Before: Verbose Match Expression

```php
match ($morphType) {
    MorphType::ULID => $table->ulidMorphs('commentable'),
    MorphType::UUID => $table->uuidMorphs('commentable'),
    MorphType::Numeric => $table->numericMorphs('commentable'),
    MorphType::String => $table->morphs('commentable'),
};
```

### After: Clean Macro

```php
$table->variableMorphs('commentable', $morphType);
```

## Examples

### Comments System with ULIDs

```php
use Cline\VariableKeys\Enums\MorphType;

Schema::create('comments', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->text('body');
    $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();

    // Comments can belong to posts, videos, etc.
    $table->variableMorphs('commentable', MorphType::ULID);

    $table->timestamps();
});
```

### Tagging System with Numeric IDs

```php
Schema::create('taggables', function (Blueprint $table) {
    $table->id();

    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

    // Tags can be attached to posts, products, etc.
    $table->variableMorphs('taggable', MorphType::Numeric);

    $table->unique(['tag_id', 'taggable_type', 'taggable_id']);
    $table->timestamps();
});
```

### Media Library with UUIDs

```php
Schema::create('media', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('file_name');
    $table->string('mime_type');
    $table->unsignedBigInteger('size');

    // Media can be attached to users, posts, products, etc.
    $table->variableMorphs('mediable', MorphType::UUID);

    $table->timestamps();
});
```

### Activity Log with Nullable Subject

```php
Schema::create('activity_log', function (Blueprint $table) {
    $table->id();
    $table->string('description');

    $table->variableMorphs('causer', MorphType::ULID);

    // Subject is optional (some activities don't have a subject)
    $table->variableMorphs('subject', MorphType::ULID, nullable: true);

    $table->timestamps();
});
```

### Notification System

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->text('data');

    // The entity that can receive notifications
    $table->variableMorphs('notifiable', MorphType::UUID);

    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

## Choosing the Right Morph Type

| Type | Use When | Characteristics |
|------|----------|----------------|
| **String** | Default, flexible setup | Auto-detects column type |
| **Numeric** | Integer primary keys | Efficient, traditional IDs |
| **UUID** | UUID primary keys | Globally unique, random |
| **ULID** | ULID primary keys | Sortable, time-ordered |

## Relationship Configuration

After creating the morph columns in your migration, define the relationship in your models:

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}

class Post extends Model
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
```

The `variablePrimaryKey()` macro replaces verbose match expressions with a clean, type-safe method for creating primary key columns based on your application's configuration.

## Basic Usage

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::create('users', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::ID);
    $table->string('name');
    $table->timestamps();
});
```

## Available Primary Key Types

### Auto-Incrementing Integer (ID)

Traditional sequential numeric IDs. Simplest option but reveals record count and ordering.

```php
$table->variablePrimaryKey(PrimaryKeyType::ID);
```

**Equivalent to:**
```php
$table->id();
```

### ULID (Universally Unique Lexicographically Sortable Identifier)

26-character case-insensitive strings that are time-ordered and globally unique. Better performance than UUIDs while maintaining sortability.

```php
$table->variablePrimaryKey(PrimaryKeyType::ULID);
```

**Equivalent to:**
```php
$table->ulid('id')->primary();
```

### UUID (Universally Unique Identifier)

36-character strings (32 hex digits plus 4 hyphens) that are globally unique and cryptographically random. Use when global uniqueness is required without chronological ordering.

```php
$table->variablePrimaryKey(PrimaryKeyType::UUID);
```

**Equivalent to:**
```php
$table->uuid('id')->primary();
```

## Custom Column Name

Specify a custom column name as the second parameter:

```php
$table->variablePrimaryKey(PrimaryKeyType::ULID, 'user_id');
```

## Configuration-Driven Primary Keys

Centralize your primary key type configuration:

```php
// config/database.php
return [
    'primary_key_type' => env('DB_PRIMARY_KEY_TYPE', 'id'),
];
```

Then use it in migrations:

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;

$primaryKeyType = PrimaryKeyType::tryFrom(config('database.primary_key_type'))
    ?? PrimaryKeyType::ID;

Schema::create('users', function (Blueprint $table) use ($primaryKeyType) {
    $table->variablePrimaryKey($primaryKeyType);
    $table->string('name');
    $table->timestamps();
});
```

## Choosing the Right Type

| Type | Use When | Characteristics |
|------|----------|----------------|
| **ID** | Traditional apps, simple requirements | Sequential, predictable, efficient |
| **ULID** | Distributed systems, time-ordered data | Sortable, globally unique, URL-safe |
| **UUID** | Maximum randomness, global uniqueness | Cryptographically random, non-sequential |

## Examples

### Simple ID

```php
Schema::create('posts', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::ID);
    $table->string('title');
    $table->text('body');
    $table->timestamps();
});
```

### ULID for Distributed System

```php
Schema::create('events', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::ULID);
    $table->string('type');
    $table->json('payload');
    $table->timestamps();
});
```

### UUID for External API Integration

```php
Schema::create('api_resources', function (Blueprint $table) {
    $table->variablePrimaryKey(PrimaryKeyType::UUID);
    $table->string('external_id');
    $table->json('data');
    $table->timestamps();
});
```
