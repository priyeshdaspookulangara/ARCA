<?php

namespace Modules\CRM\CustomerMaster\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Relationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_customer_id',
        'child_customer_id',
        'type' // e.g., 'subsidiary', 'branch'
    ];

    public function parentCustomer()
    {
        return $this->belongsTo(Customer::class, 'parent_customer_id');
    }

    public function childCustomer()
    {
        return $this->belongsTo(Customer::class, 'child_customer_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\RelationshipFactory::new();
    }
}