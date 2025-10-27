<?php

namespace Modules\TaxEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxLedger extends Model
{
    use HasFactory;

    protected $fillable = ['tax_code_id', 'period', 'collected', 'payable', 'difference'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }
}
