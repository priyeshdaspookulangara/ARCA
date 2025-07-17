<?php

return [
    'promotion' => [
        'type' => 'workflow',
        'supports' => ['Modules\HR\Models\PersonnelActionRequest'],
        'places' => ['draft', 'pending_manager_approval', 'pending_hrbp_approval', 'approved', 'rejected', 'implemented'],
        'transitions' => [
            'submit_for_approval' => [
                'from' => 'draft',
                'to' => 'pending_manager_approval',
            ],
            'manager_approve' => [
                'from' => 'pending_manager_approval',
                'to' => 'pending_hrbp_approval',
            ],
            'hrbp_approve' => [
                'from' => 'pending_hrbp_approval',
                'to' => 'approved',
            ],
            'reject' => [
                'from' => ['pending_manager_approval', 'pending_hrbp_approval'],
                'to' => 'rejected',
            ],
            'implement' => [
                'from' => 'approved',
                'to' => 'implemented',
            ],
        ],
    ],
];
