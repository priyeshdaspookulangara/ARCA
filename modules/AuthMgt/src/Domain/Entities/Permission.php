<?php

namespace Modules\AuthMgt\Domain\Entities;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Permission extends Pivot
{
    protected $table = 'permissions';

    protected $fillable = ['role_id', 'auth_object_id', 'actions', 'restrictions'];

    protected $casts = [
        'actions' => 'array',
        'restrictions' => 'array',
    ];
}