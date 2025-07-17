<?php

namespace Modules\Fina\FI\AA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetClass extends Model
{
    protected $table = 'fina_aa_asset_classes';

    protected $fillable = [
        'code',
        'name',
        'gl_account_determination_key',
    ];
}
