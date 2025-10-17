<?php

namespace Modules\CRM\Service\Application;

use Modules\CRM\Service\Domain\Model\ServiceTicket;

class ServiceTicketService
{
    public function createTicket(array $data): ServiceTicket
    {
        return ServiceTicket::create($data);
    }

    public function assignTicket(ServiceTicket $ticket, int $userId)
    {
        $ticket->assigned_to = $userId;
        $ticket->status = 'in_progress';
        $ticket->save();
    }

    public function resolveTicket(ServiceTicket $ticket)
    {
        $ticket->status = 'closed';
        $ticket->save();
    }
}