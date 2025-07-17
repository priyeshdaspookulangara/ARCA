<?php

namespace Modules\Fina\FI\AA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    protected $table = 'fina_aa_asset_transactions';

    protected $fillable = [
        'gl_document_header_id',
        'asset_master_id',
        'transaction_type',
        'amount',
        'posting_date',
    ];
}
