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

use function afterEach;
use function beforeEach;
use function describe;
use function expect;
use function test;

describe('variableForeignKey macro', function (): void {
    beforeEach(function (): void {
        Schema::create('parent_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::ID);
        });
    });

    afterEach(function (): void {
        Schema::dropIfExists('child_table');
        Schema::dropIfExists('parent_table');
    });

    test('creates foreign key with auto-incrementing ID for PrimaryKeyType::ID', function (): void {
        Schema::create('child_table', function (Blueprint $table): void {
            $table->variableForeignKey('parent_id', PrimaryKeyType::ID)->constrained('parent_table');
        });

        expect(Schema::hasColumn('child_table', 'parent_id'))->toBeTrue();
    });

    test('creates foreign key with ULID for PrimaryKeyType::ULID', function (): void {
        Schema::drop('parent_table');
        Schema::create('parent_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::ULID);
        });

        Schema::create('child_table', function (Blueprint $table): void {
            $table->variableForeignKey('parent_id', PrimaryKeyType::ULID)->constrained('parent_table');
        });

        expect(Schema::hasColumn('child_table', 'parent_id'))->toBeTrue();
    });

    test('creates foreign key with UUID for PrimaryKeyType::UUID', function (): void {
        Schema::drop('parent_table');
        Schema::create('parent_table', function (Blueprint $table): void {
            $table->variablePrimaryKey(PrimaryKeyType::UUID);
        });

        Schema::create('child_table', function (Blueprint $table): void {
            $table->variableForeignKey('parent_id', PrimaryKeyType::UUID)->constrained('parent_table');
        });

        expect(Schema::hasColumn('child_table', 'parent_id'))->toBeTrue();
    });
});
