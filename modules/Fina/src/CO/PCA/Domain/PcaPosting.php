<?php

namespace Modules\Fina\CO\PCA\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Fina\FI\GL\Domain\GLAccount;

class PcaPosting extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pca_postings';

    protected $fillable = [
        'profit_center_id',
        'gl_account_id',
        'document_number',
        'amount',
        'posting_date',
        'description',
    ];

    public function profitCenter()
    {
        return $this->belongsTo(ProfitCenter::class, 'profit_center_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }
}