<?php

namespace Modules\AuthMgt\Application\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roleId;

    public function __construct(int $roleId)
    {
        $this->roleId = $roleId;
    }
}