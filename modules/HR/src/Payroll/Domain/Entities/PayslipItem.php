<?php

namespace Modules\HR\Payroll\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\Payroll\Domain\Entities\Payslip;

class PayslipItem extends Model
{
    use HasFactory;

    protected $table = 'hr_payslip_items';

    // No soft deletes for payslip items. They belong to a payslip.

    protected $fillable = [
        'hr_payslip_id',
        'item_type',
        'description',
        'amount',
        'is_pre_tax',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_pre_tax' => 'boolean',
    ];

    /**
     * Get the payslip this item belongs to.
     */
    public function payslip()
    {
        return $this->belongsTo(Payslip::class, 'hr_payslip_id');
    }

    // Define constants for item types
    public const TYPE_EARNING = 'earning';
    public const TYPE_DEDUCTION = 'deduction';
}
