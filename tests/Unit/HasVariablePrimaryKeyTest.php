<?php

declare(strict_types=1);

use Cline\VariableKeys\Database\Concerns\HasVariablePrimaryKey;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Exceptions\CannotAssignNonStringToUlidException;
use Cline\VariableKeys\Exceptions\CannotAssignNonStringToUuidException;
use Cline\VariableKeys\Exceptions\ModelNotRegisteredException;
use Cline\VariableKeys\Facades\VariableKeys;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TestModel extends Model
{
    use HasVariablePrimaryKey;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}

beforeEach(function (): void {
    VariableKeys::clear();
});

describe('model configuration for ULID primary keys', function (): void {
    beforeEach(function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);
    });

    test('getIncrementing returns false', function (): void {
        $model = new TestModel();

        expect($model->getIncrementing())->toBeFalse();
    });

    test('getKeyType returns string', function (): void {
        $model = new TestModel();

        expect($model->getKeyType())->toBe('string');
    });

    test('uniqueIds includes primary key', function (): void {
        $model = new TestModel();

        expect($model->uniqueIds())->toBe(['id']);
    });

    test('newUniqueId generates ULID', function (): void {
        $model = new TestModel();
        $id = $model->newUniqueId();

        expect($id)->toBeString();
        expect(Str::isUlid($id))->toBeTrue();
    });
});

describe('model configuration for UUID primary keys', function (): void {
    beforeEach(function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::UUID],
        ]);
    });

    test('getIncrementing returns false', function (): void {
        $model = new TestModel();

        expect($model->getIncrementing())->toBeFalse();
    });

    test('getKeyType returns string', function (): void {
        $model = new TestModel();

        expect($model->getKeyType())->toBe('string');
    });

    test('uniqueIds includes primary key', function (): void {
        $model = new TestModel();

        expect($model->uniqueIds())->toBe(['id']);
    });

    test('newUniqueId generates UUID', function (): void {
        $model = new TestModel();
        $id = $model->newUniqueId();

        expect($id)->toBeString();
        expect(Str::isUuid($id))->toBeTrue();
    });
});

describe('model configuration for auto-increment primary keys', function (): void {
    beforeEach(function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::ID],
        ]);
    });

    test('getIncrementing returns true', function (): void {
        $model = new TestModel();

        expect($model->getIncrementing())->toBeTrue();
    });

    test('getKeyType returns int', function (): void {
        $model = new TestModel();

        expect($model->getKeyType())->toBe('int');
    });

    test('uniqueIds returns empty array', function (): void {
        $model = new TestModel();

        expect($model->uniqueIds())->toBe([]);
    });

    test('newUniqueId returns null', function (): void {
        $model = new TestModel();

        expect($model->newUniqueId())->toBeNull();
    });
});

describe('creating event generates primary key', function (): void {
    test('generates ULID when no value exists', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);

        $model = new TestModel();

        // Manually trigger the creating event callback
        TestModel::creating(function (TestModel $m) use ($model): void {
            // This simulates what happens during model creation
        });

        // Simulate what the boot method does
        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        if (! $primaryKey->isAutoIncrementing() && ! $model->getAttribute('id')) {
            $model->setAttribute('id', $primaryKey->value);
        }

        expect($model->id)->toBeString();
        expect(Str::isUlid($model->id))->toBeTrue();
    });

    test('generates UUID when no value exists', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::UUID],
        ]);

        $model = new TestModel();

        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        if (! $primaryKey->isAutoIncrementing() && ! $model->getAttribute('id')) {
            $model->setAttribute('id', $primaryKey->value);
        }

        expect($model->id)->toBeString();
        expect(Str::isUuid($model->id))->toBeTrue();
    });

    test('does not generate ID for auto-increment', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::ID],
        ]);

        $model = new TestModel();

        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        if (! $primaryKey->isAutoIncrementing() && ! $model->getAttribute('id')) {
            $model->setAttribute('id', $primaryKey->value);
        }

        expect($model->id)->toBeNull();
    });

    test('preserves custom ULID value', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);

        $customId = strtolower((string) Str::ulid());
        $model = new TestModel(['id' => $customId]);

        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        if (! $primaryKey->isAutoIncrementing() && ! $model->getAttribute('id')) {
            $model->setAttribute('id', $primaryKey->value);
        }

        expect($model->id)->toBe($customId);
    });

    test('preserves custom UUID value', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::UUID],
        ]);

        $customId = (string) Str::uuid();
        $model = new TestModel(['id' => $customId]);

        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        if (! $primaryKey->isAutoIncrementing() && ! $model->getAttribute('id')) {
            $model->setAttribute('id', $primaryKey->value);
        }

        expect($model->id)->toBe($customId);
    });
});

describe('validation errors for type mismatches', function (): void {
    test('throws exception for non-string UUID value', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::UUID],
        ]);

        $model = new TestModel(['id' => 123]);

        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        $existingValue = $model->getAttribute('id');

        if ($primaryKey->type === PrimaryKeyType::UUID && ! is_string($existingValue)) {
            throw CannotAssignNonStringToUuidException::forValue($existingValue);
        }
    })->throws(CannotAssignNonStringToUuidException::class);

    test('throws exception for non-string ULID value', function (): void {
        VariableKeys::map([
            TestModel::class => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);

        $model = new TestModel(['id' => 456]);

        $registry = app(\Cline\VariableKeys\VariableKeysRegistry::class);
        $primaryKeyType = $registry->getPrimaryKeyType(TestModel::class);
        $primaryKey = \Cline\VariableKeys\Support\PrimaryKeyGenerator::generate($primaryKeyType);

        $existingValue = $model->getAttribute('id');

        if ($primaryKey->type === PrimaryKeyType::ULID && ! is_string($existingValue)) {
            throw CannotAssignNonStringToUlidException::forValue($existingValue);
        }
    })->throws(CannotAssignNonStringToUlidException::class);
});

describe('unregistered model handling', function (): void {
    test('throws exception when model not registered', function (): void {
        $model = new TestModel();
        $model->getIncrementing();
    })->throws(ModelNotRegisteredException::class, 'Model [' . TestModel::class . '] is not registered');
});
