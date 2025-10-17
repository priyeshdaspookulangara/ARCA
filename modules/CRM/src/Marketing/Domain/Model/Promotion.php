<?php

namespace Modules\CRM\Marketing\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'type',
        'discount_value',
        'discount_type'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\PromotionFactory::new();
    }
}