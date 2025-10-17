<?php

namespace Modules\CRM\Loyalty\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_program_id',
        'name',
        'min_points',
        'multiplier'
    ];

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\LoyaltyTierFactory::new();
    }
}