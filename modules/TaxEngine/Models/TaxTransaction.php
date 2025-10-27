<?php

namespace Modules\TaxEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['source_module', 'reference_id', 'tax_code_id', 'taxable_amount', 'tax_amount', 'status'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }
}
