<?php

namespace Modules\AuthMgt\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class AuthUser extends Model
{
    protected $fillable = ['user_id'];

    /**
     * The user this authorization profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The roles assigned to this user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'auth_user_role');
    }
}