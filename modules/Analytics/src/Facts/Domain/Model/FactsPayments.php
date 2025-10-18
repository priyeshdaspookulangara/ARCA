<?php

namespace Modules\Analytics\Facts\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FactsPayments extends Model
{
    use HasFactory;

    protected $table = 'facts_payments';

    protected $fillable = [
        'payment_id',
        'sale_id',
        'amount',
        'mode',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\FactsPaymentsFactory::new();
    }
}