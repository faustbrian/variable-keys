<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Support\PrimaryKeyGenerator;
use Illuminate\Support\Str;

describe('generate method', function (): void {
    test('generates ULID for ULID type', function (): void {
        $result = PrimaryKeyGenerator::generate(PrimaryKeyType::ULID);

        expect($result->type)->toBe(PrimaryKeyType::ULID);
        expect($result->value)->toBeString();
        expect($result->value)->toHaveLength(26);
        expect(Str::isUlid($result->value))->toBeTrue();
    });

    test('generates UUID for UUID type', function (): void {
        $result = PrimaryKeyGenerator::generate(PrimaryKeyType::UUID);

        expect($result->type)->toBe(PrimaryKeyType::UUID);
        expect($result->value)->toBeString();
        expect($result->value)->toHaveLength(36);
        expect(Str::isUuid($result->value))->toBeTrue();
    });

    test('returns null for ID type', function (): void {
        $result = PrimaryKeyGenerator::generate(PrimaryKeyType::ID);

        expect($result->type)->toBe(PrimaryKeyType::ID);
        expect($result->value)->toBeNull();
    });

    test('generates unique values on each call', function (): void {
        $result1 = PrimaryKeyGenerator::generate(PrimaryKeyType::ULID);
        $result2 = PrimaryKeyGenerator::generate(PrimaryKeyType::ULID);

        expect($result1->value)->not->toBe($result2->value);
    });

    test('generates lowercase ULIDs', function (): void {
        $result = PrimaryKeyGenerator::generate(PrimaryKeyType::ULID);

        expect($result->value)->toBe(mb_strtolower((string) $result->value));
    });

    test('generates lowercase UUIDs', function (): void {
        $result = PrimaryKeyGenerator::generate(PrimaryKeyType::UUID);

        expect($result->value)->toBe(mb_strtolower((string) $result->value));
    });
});

describe('enrichPivotData method', function (): void {
    test('adds generated ULID to pivot data', function (): void {
        $data = ['role_id' => 1, 'assigned_at' => now()];
        $enriched = PrimaryKeyGenerator::enrichPivotData(PrimaryKeyType::ULID, $data);

        expect($enriched)->toHaveKey('id');
        expect($enriched['id'])->toBeString();
        expect(Str::isUlid($enriched['id']))->toBeTrue();
        expect($enriched)->toHaveKey('role_id');
        expect($enriched)->toHaveKey('assigned_at');
    });

    test('adds generated UUID to pivot data', function (): void {
        $data = ['role_id' => 1];
        $enriched = PrimaryKeyGenerator::enrichPivotData(PrimaryKeyType::UUID, $data);

        expect($enriched)->toHaveKey('id');
        expect(Str::isUuid($enriched['id']))->toBeTrue();
    });

    test('does not modify data for ID type', function (): void {
        $data = ['role_id' => 1, 'assigned_at' => now()];
        $enriched = PrimaryKeyGenerator::enrichPivotData(PrimaryKeyType::ID, $data);

        expect($enriched)->toBe($data);
        expect($enriched)->not->toHaveKey('id');
    });
});

describe('enrichPivotDataForIds method', function (): void {
    test('enriches data for multiple IDs', function (): void {
        $ids = [1, 2, 3];
        $data = ['assigned_at' => now()];
        $enriched = PrimaryKeyGenerator::enrichPivotDataForIds(PrimaryKeyType::ULID, $ids, $data);

        expect($enriched)->toHaveCount(3);
        expect($enriched[1])->toHaveKey('id');
        expect($enriched[2])->toHaveKey('id');
        expect($enriched[3])->toHaveKey('id');

        // Each ID should be unique
        expect($enriched[1]['id'])->not->toBe($enriched[2]['id']);
        expect($enriched[2]['id'])->not->toBe($enriched[3]['id']);
    });

    test('preserves original data for each ID', function (): void {
        $ids = [1, 2];
        $timestamp = now();
        $data = ['assigned_at' => $timestamp];
        $enriched = PrimaryKeyGenerator::enrichPivotDataForIds(PrimaryKeyType::UUID, $ids, $data);

        expect($enriched[1]['assigned_at'])->toBe($timestamp);
        expect($enriched[2]['assigned_at'])->toBe($timestamp);
    });

    test('does not add IDs for auto-increment type', function (): void {
        $ids = [1, 2];
        $data = ['assigned_at' => now()];
        $enriched = PrimaryKeyGenerator::enrichPivotDataForIds(PrimaryKeyType::ID, $ids, $data);

        expect($enriched[1])->not->toHaveKey('id');
        expect($enriched[2])->not->toHaveKey('id');
    });
});
