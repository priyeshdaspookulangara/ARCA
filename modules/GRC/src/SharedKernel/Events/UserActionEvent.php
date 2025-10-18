<?php

namespace Modules\GRC\SharedKernel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActionEvent
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $module;
    public $action;
    public $entity;
    public $payload;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $module, $action, $entity, $payload)
    {
        $this->userId = $userId;
        $this->module = $module;
        $this->action = $action;
        $this->entity = $entity;
        $this->payload = $payload;
    }
}