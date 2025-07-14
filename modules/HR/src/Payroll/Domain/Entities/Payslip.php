<?php

namespace Modules\HR\Payroll\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;
use Modules\HR\Payroll\Domain\Entities\PayslipItem;

class Payslip extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_payslips';

    protected $fillable = [
        'hr_employee_id',
        'hr_payroll_period_id',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'status',
        'notes',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    /**
     * Get the employee for this payslip.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'hr_employee_id');
    }

    /**
     * Get the payroll period for this payslip.
     */
    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'hr_payroll_period_id');
    }

    /**
     * Get all items (earnings and deductions) for this payslip.
     */
    public function items()
    {
        return $this->hasMany(PayslipItem::class, 'hr_payslip_id');
    }

    /**
     * Get only the earning items for this payslip.
     */
    public function earnings()
    {
        return $this->hasMany(PayslipItem::class, 'hr_payslip_id')->where('item_type', 'earning');
    }

    /**
     * Get only the deduction items for this payslip.
     */
    public function deductions()
    {
        return $this->hasMany(PayslipItem::class, 'hr_payslip_id')->where('item_type', 'deduction');
    }


    // Define constants for status types
    public const STATUS_DRAFT = 'draft';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
}
