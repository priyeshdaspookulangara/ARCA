<?php

namespace Modules\GRC\AuditMgt\Application\Listeners;

use Modules\GRC\SharedKernel\Events\UserActionEvent;
use Modules\GRC\AuditMgt\Domain\Model\AuditLog;

class LogUserActionEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Modules\GRC\SharedKernel\Events\UserActionEvent  $event
     * @return void
     */
    public function handle(UserActionEvent $event)
    {
        $lastLog = AuditLog::latest()->first();
        $previousHash = $lastLog ? $lastLog->hash : null;

        $payload = [
            'module' => $event->module,
            'entity_type' => get_class($event->entity),
            'entity_id' => $event->entity->id,
            'action' => $event->action,
            'payload_json' => $event->payload,
            'user_id' => $event->userId,
            'previous_hash' => $previousHash,
        ];

        $payload['hash'] = hash('sha256', json_encode($payload));

        AuditLog::create($payload);
    }
}