<?php

declare(strict_types=1);

namespace Cline\VariableKeys\Support;

use Cline\VariableKeys\Enums\PrimaryKeyType;

final readonly class PrimaryKeyValue
{
    public function __construct(
        public PrimaryKeyType $type,
        public ?string $value,
    ) {}

    /**
     * Determine if the primary key uses auto-incrementing.
     */
    public function isAutoIncrementing(): bool
    {
        return $this->type === PrimaryKeyType::ID;
    }

    /**
     * Determine if the primary key requires a value to be set.
     */
    public function requiresValue(): bool
    {
        return ! $this->isAutoIncrementing();
    }
}
