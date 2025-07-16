<?php

namespace Modules\HR\Payroll\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\Payroll\Domain\Entities\Payslip;
use Modules\HR\Payroll\Domain\Entities\EarningType;
use Modules\HR\Payroll\Domain\Entities\DeductionType;

class PayslipItem extends Model
{
    use HasFactory;

    protected $table = 'hr_payslip_items';

    // No soft deletes for payslip items. They belong to a payslip.

    protected $fillable = [
        'hr_payslip_id',
        'earning_type_id',
        'deduction_type_id',
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

    /**
     * Get the earning type for this item (if applicable).
     */
    public function earningType()
    {
        return $this->belongsTo(EarningType::class, 'earning_type_id');
    }

    /**
     * Get the deduction type for this item (if applicable).
     */
    public function deductionType()
    {
        return $this->belongsTo(DeductionType::class, 'deduction_type_id');
    }


    // Define constants for item types
    public const TYPE_EARNING = 'earning';
    public const TYPE_DEDUCTION = 'deduction';
}
