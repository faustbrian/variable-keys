<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Cline\VariableKeys\Enums\MorphType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function afterEach;
use function describe;
use function expect;
use function test;

describe('variableMorphs macro', function (): void {
    afterEach(function (): void {
        Schema::dropIfExists('test_table');
    });

    test('creates string morphs for MorphType::String', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::String);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates numeric morphs for MorphType::Numeric', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::Numeric);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates UUID morphs for MorphType::UUID', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::UUID);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates ULID morphs for MorphType::ULID', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::ULID);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates nullable string morphs when nullable is true', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::String, nullable: true);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates nullable UUID morphs when nullable is true', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::UUID, nullable: true);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates nullable ULID morphs when nullable is true', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::ULID, nullable: true);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });

    test('creates nullable numeric morphs when nullable is true', function (): void {
        Schema::create('test_table', function (Blueprint $table): void {
            $table->variableMorphs('subject', MorphType::Numeric, nullable: true);
        });

        expect(Schema::hasColumn('test_table', 'subject_type'))->toBeTrue()
            ->and(Schema::hasColumn('test_table', 'subject_id'))->toBeTrue();
    });
});
