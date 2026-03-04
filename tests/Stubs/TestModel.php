<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Stubs;

use Cline\VariableKeys\Database\Concerns\HasVariablePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 * @internal
 */
final class TestModel extends Model
{
    use HasFactory;
    use HasVariablePrimaryKey;

    #[Override()]
    public $timestamps = false;

    #[Override()]
    protected $table = 'test_models';

    #[Override()]
    protected $guarded = [];
}
