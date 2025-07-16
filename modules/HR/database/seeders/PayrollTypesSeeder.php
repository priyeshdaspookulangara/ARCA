<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Payroll\Domain\Entities\EarningType;
use Modules\HR\Payroll\Domain\Entities\DeductionType;

class PayrollTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Basic Earning Types
        EarningType::updateOrCreate(['name' => 'Basic Salary'], ['is_taxable' => true, 'is_active' => true]);
        EarningType::updateOrCreate(['name' => 'Overtime Pay'], ['is_taxable' => true, 'is_active' => true]);
        EarningType::updateOrCreate(['name' => 'Bonus'], ['is_taxable' => true, 'is_active' => true]);
        EarningType::updateOrCreate(['name' => 'Commission'], ['is_taxable' => true, 'is_active' => true]);
        EarningType::updateOrCreate(['name' => 'Travel Allowance'], ['is_taxable' => false, 'is_active' => true]);

        // Basic Deduction Types
        DeductionType::updateOrCreate(['name' => 'Income Tax'], ['is_pre_tax' => false, 'is_active' => true]);
        DeductionType::updateOrCreate(['name' => 'Health Insurance'], ['is_pre_tax' => true, 'is_active' => true]);
        DeductionType::updateOrCreate(['name' => 'Pension Contribution'], ['is_pre_tax' => true, 'is_active' => true]);
        DeductionType::updateOrCreate(['name' => 'Loan Repayment'], ['is_pre_tax' => false, 'is_active' => true]);
    }
}
