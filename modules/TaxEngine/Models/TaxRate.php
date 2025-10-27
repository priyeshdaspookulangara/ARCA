<?php

namespace Modules\TaxEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = ['tax_code_id', 'country', 'state', 'rate', 'effective_from', 'effective_to'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }
}
