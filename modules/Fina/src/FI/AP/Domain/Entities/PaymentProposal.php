<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentProposal extends Model
{
    use HasFactory;

    protected $table = 'fina_ap_payment_proposals';

    protected $fillable = [
        'payment_run_id',
        'invoice_id',
        'status',
    ];

    public function paymentRun()
    {
        return $this->belongsTo(PaymentRun::class, 'payment_run_id');
    }

    public function invoice()
    {
        return $this->belongsTo(APInvoiceHeader::class, 'invoice_id');
    }
}