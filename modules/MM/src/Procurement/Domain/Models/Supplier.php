<?php

namespace Modules\MM\Procurement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\MaterialMaster\Domain\Models\Material;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'mm_suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class, 'default_supplier_id');
    }
}