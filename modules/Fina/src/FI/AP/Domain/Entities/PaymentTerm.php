<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    protected $table = 'fina_payment_terms';

    protected $fillable = [
        'code',
        'description',
        'rules',
    ];

    protected $casts = [
        'rules' => 'json',
    ];
}
