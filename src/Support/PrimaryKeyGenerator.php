<?php

declare(strict_types=1);

namespace Cline\VariableKeys\Support;

use Cline\VariableKeys\Enums\PrimaryKeyType;
use Illuminate\Support\Str;

final class PrimaryKeyGenerator
{
    /**
     * Generate a primary key value based on the given type.
     */
    public static function generate(PrimaryKeyType $type): PrimaryKeyValue
    {
        return new PrimaryKeyValue(
            type: $type,
            value: match ($type) {
                PrimaryKeyType::ULID => strtolower((string) Str::ulid()),
                PrimaryKeyType::UUID => (string) Str::uuid(),
                PrimaryKeyType::ID => null,
            },
        );
    }

    /**
     * Enrich pivot table data with generated primary key.
     *
     * Adds a generated 'id' field to the pivot data when using ULID or UUID.
     * For auto-incrementing IDs, returns the data unchanged.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public static function enrichPivotData(PrimaryKeyType $type, array $data): array
    {
        $key = self::generate($type);

        if ($key->requiresValue()) {
            $data['id'] = $key->value;
        }

        return $data;
    }

    /**
     * Enrich pivot data for multiple IDs with generated primary keys.
     *
     * Generates a unique primary key for each ID in bulk operations.
     *
     * @param array<int, mixed>    $ids
     * @param array<string, mixed> $data
     *
     * @return array<int, array<string, mixed>>
     */
    public static function enrichPivotDataForIds(PrimaryKeyType $type, array $ids, array $data): array
    {
        $enriched = [];

        foreach ($ids as $id) {
            $enriched[$id] = self::enrichPivotData($type, $data);
        }

        return $enriched;
    }
}
