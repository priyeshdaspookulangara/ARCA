<?php

namespace Modules\TaxEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    use HasFactory;

    protected $fillable = ['tax_code_id', 'criteria', 'expression', 'is_compound'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }
}
