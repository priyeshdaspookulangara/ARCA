<?php

return [
    'name' => 'ProjectSystem',
    'description' => 'Project System Module for managing full project lifecycles.',
    // Enable/disable PS functional domains/features
    'structuring' => ['enabled' => true],
    'scheduling' => ['enabled' => true],
    'costing' => ['enabled' => true],
    'resourcemgt' => ['enabled' => true],
    'materialmgt' => ['enabled' => true],
    'execution' => ['enabled' => true],
    'closing' => ['enabled' => true],

    'defaults' => [
        'project_profile' => 'DEFAULT_PROFILE',
        'currency' => 'USD', // Default currency for projects if not specified
    ],
];
