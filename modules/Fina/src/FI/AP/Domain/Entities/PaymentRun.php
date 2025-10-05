<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentRun extends Model
{
    use HasFactory;

    protected $table = 'fina_ap_payment_runs';

    protected $fillable = [
        'run_date',
        'status',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function proposals()
    {
        return $this->hasMany(PaymentProposal::class, 'payment_run_id');
    }
}