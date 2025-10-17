<?php

namespace Modules\CRM\Product\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'source', // e.g., 'cross-sell', 'up-sell', 'manual'
        'score',
    ];

    public function customer()
    {
        return $this->belongsTo(\Modules\CRM\CustomerMaster\Domain\Model\Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductCatalog::class, 'product_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\RecommendationFactory::new();
    }
}