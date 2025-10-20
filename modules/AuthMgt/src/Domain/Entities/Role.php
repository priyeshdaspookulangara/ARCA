<?php

namespace Modules\AuthMgt\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'scope', 'parent_id'];

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(AuthUser::class, 'auth_user_role');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AuthObject::class, 'permissions')
                    ->withPivot('actions', 'restrictions')
                    ->withTimestamps();
    }

    /**
     * Child roles for hierarchical roles.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_id');
    }
}