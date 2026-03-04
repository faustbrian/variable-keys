<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\VariableKeys\Enums\MorphType;
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Exceptions\ModelNotRegisteredException;
use Cline\VariableKeys\VariableKeysRegistry;

beforeEach(function (): void {
    $this->registry = new VariableKeysRegistry();
});

describe('map method', function (): void {
    test('registers model with primary key type', function (): void {
        $this->registry->map([
            'App\Models\User' => [
                'primary_key_type' => PrimaryKeyType::ULID,
            ],
        ]);

        expect($this->registry->isRegistered('App\Models\User'))->toBeTrue();
        expect($this->registry->getPrimaryKeyType('App\Models\User'))->toBe(PrimaryKeyType::ULID);
    });

    test('registers model with morph type', function (): void {
        $this->registry->map([
            'App\Models\User' => [
                'primary_key_type' => PrimaryKeyType::UUID,
                'morph_type' => MorphType::UUID,
            ],
        ]);

        expect($this->registry->getMorphType('App\Models\User'))->toBe(MorphType::UUID);
    });

    test('registers multiple models', function (): void {
        $this->registry->map([
            'App\Models\User' => ['primary_key_type' => PrimaryKeyType::ULID],
            'App\Models\Post' => ['primary_key_type' => PrimaryKeyType::UUID],
            'App\Models\Comment' => ['primary_key_type' => PrimaryKeyType::ID],
        ]);

        expect($this->registry->isRegistered('App\Models\User'))->toBeTrue();
        expect($this->registry->isRegistered('App\Models\Post'))->toBeTrue();
        expect($this->registry->isRegistered('App\Models\Comment'))->toBeTrue();
    });
});

describe('getPrimaryKeyType method', function (): void {
    test('returns registered primary key type', function (): void {
        $this->registry->map([
            'App\Models\User' => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);

        expect($this->registry->getPrimaryKeyType('App\Models\User'))->toBe(PrimaryKeyType::ULID);
    });

    test('throws exception for unregistered model', function (): void {
        $this->registry->getPrimaryKeyType('App\Models\User');
    })->throws(ModelNotRegisteredException::class, 'Model [App\Models\User] is not registered with VariableKeys');
});

describe('getMorphType method', function (): void {
    test('returns registered morph type', function (): void {
        $this->registry->map([
            'App\Models\User' => [
                'primary_key_type' => PrimaryKeyType::UUID,
                'morph_type' => MorphType::UUID,
            ],
        ]);

        expect($this->registry->getMorphType('App\Models\User'))->toBe(MorphType::UUID);
    });

    test('throws exception for unregistered model', function (): void {
        $this->registry->getMorphType('App\Models\User');
    })->throws(ModelNotRegisteredException::class);

    test('throws exception when morph type not configured', function (): void {
        $this->registry->map([
            'App\Models\User' => ['primary_key_type' => PrimaryKeyType::UUID],
        ]);

        $this->registry->getMorphType('App\Models\User');
    })->throws(ModelNotRegisteredException::class);
});

describe('isRegistered method', function (): void {
    test('returns true for registered model', function (): void {
        $this->registry->map([
            'App\Models\User' => ['primary_key_type' => PrimaryKeyType::ULID],
        ]);

        expect($this->registry->isRegistered('App\Models\User'))->toBeTrue();
    });

    test('returns false for unregistered model', function (): void {
        expect($this->registry->isRegistered('App\Models\User'))->toBeFalse();
    });
});

describe('clear method', function (): void {
    test('removes all registered models', function (): void {
        $this->registry->map([
            'App\Models\User' => ['primary_key_type' => PrimaryKeyType::ULID],
            'App\Models\Post' => ['primary_key_type' => PrimaryKeyType::UUID],
        ]);

        expect($this->registry->isRegistered('App\Models\User'))->toBeTrue();
        expect($this->registry->isRegistered('App\Models\Post'))->toBeTrue();

        $this->registry->clear();

        expect($this->registry->isRegistered('App\Models\User'))->toBeFalse();
        expect($this->registry->isRegistered('App\Models\Post'))->toBeFalse();
    });
});
