<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Cline\VariableKeys\Enums\PrimaryKeyType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function describe;
use function expect;
use function test;

describe('variablePrimaryKey macro', function (): void {
    test('creates auto-incrementing ID for PrimaryKeyType::ID', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::ID);
        });

        expect(Schema::hasColumn('test_table', 'id'))->toBeTrue();
        Schema::drop('test_table');
    });

    test('creates ULID primary key for PrimaryKeyType::ULID', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::ULID);
        });

        expect(Schema::hasColumn('test_table', 'id'))->toBeTrue();
        Schema::drop('test_table');
    });

    test('creates UUID primary key for PrimaryKeyType::UUID', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::UUID);
        });

        expect(Schema::hasColumn('test_table', 'id'))->toBeTrue();
        Schema::drop('test_table');
    });

    test('accepts custom column name', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::ID, 'custom_id');
        });

        expect(Schema::hasColumn('test_table', 'custom_id'))->toBeTrue();
        Schema::drop('test_table');
    });
});
