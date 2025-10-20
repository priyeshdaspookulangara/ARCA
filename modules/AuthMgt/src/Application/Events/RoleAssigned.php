<?php

namespace Modules\AuthMgt\Application\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RoleAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $roleId;

    public function __construct(int $userId, int $roleId)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }
}