<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\VariableKeys\Support;

use Cline\VariableKeys\Enums\PrimaryKeyType;

/**
 * @psalm-immutable
 */
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
        return !$this->isAutoIncrementing();
    }
}
