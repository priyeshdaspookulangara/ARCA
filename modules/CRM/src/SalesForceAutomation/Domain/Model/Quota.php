<?php

namespace Modules\CRM\SalesForceAutomation\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quota extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'start_date',
        'end_date',
        'territory_id',
        'user_id',
    ];

    public function territory()
    {
        return $this->belongsTo(Territory::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\QuotaFactory::new();
    }
}