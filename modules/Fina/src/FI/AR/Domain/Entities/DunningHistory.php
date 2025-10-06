<?php

namespace Modules\Fina\FI\AR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DunningHistory extends Model
{
    use HasFactory;

    protected $table = 'fina_ar_dunning_history';

    protected $fillable = [
        'customer_financials_id',
        'dunning_date',
        'dunning_level',
        'dunning_notice_content',
    ];

    public function customerFinancials()
    {
        return $this->belongsTo(ARCustomerFinancials::class, 'customer_financials_id');
    }
}