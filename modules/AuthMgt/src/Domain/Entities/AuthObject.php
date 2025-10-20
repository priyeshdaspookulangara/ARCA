<?php

namespace Modules\AuthMgt\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuthObject extends Model
{
    protected $fillable = ['code', 'module', 'description', 'actions', 'status'];

    protected $casts = [
        'actions' => 'array',
    ];

    /**
     * The roles that have this auth object.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permissions')
                    ->withPivot('actions', 'restrictions')
                    ->withTimestamps();
    }
}