<?php

namespace Modules\TaxEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'description', 'category', 'status'];

    public function rates()
    {
        return $this->hasMany(TaxRate::class);
    }
}
