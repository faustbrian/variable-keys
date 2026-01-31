<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Cline\VariableKeys\VariableKeysServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Override;

/**
 * Base test case for all package tests.
 *
 * @author Brian Faust <brian@cline.sh>
 * @internal
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  Application              $app
     * @return array<int, class-string>
     */
    #[Override()]
    protected function getPackageProviders($app): array
    {
        return [
            VariableKeysServiceProvider::class,
        ];
    }
}
