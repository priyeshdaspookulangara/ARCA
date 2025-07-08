<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Standard paid annual leave entitlement.',
                'is_paid' => true,
                'default_entitlement_days' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Paid leave for sickness or medical appointments.',
                'is_paid' => true,
                'default_entitlement_days' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave taken without pay, subject to approval.',
                'is_paid' => false,
                'default_entitlement_days' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for new mothers.',
                'is_paid' => true, // Or as per policy
                'default_entitlement_days' => 90, // Example
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for new fathers.',
                'is_paid' => true, // Or as per policy
                'default_entitlement_days' => 10, // Example
                'is_active' => true,
            ],
             [
                'name' => 'Bereavement Leave',
                'description' => 'Leave due to the death of a close family member.',
                'is_paid' => true,
                'default_entitlement_days' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
