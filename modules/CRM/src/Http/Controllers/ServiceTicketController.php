<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Service\Application\ServiceTicketService;
use Modules\CRM\Service\Domain\Model\ServiceTicket;

class ServiceTicketController extends Controller
{
    private $serviceTicketService;

    public function __construct(ServiceTicketService $serviceTicketService)
    {
        $this->serviceTicketService = $serviceTicketService;
    }

    public function index()
    {
        return ServiceTicket::all();
    }

    public function store(Request $request)
    {
        $ticket = $this->serviceTicketService->createTicket($request->all());
        $ticket->save();
        return $ticket;
    }

    public function show($id)
    {
        return ServiceTicket::find($id);
    }

    public function update(Request $request, $id)
    {
        $ticket = ServiceTicket::find($id);
        $ticket->update($request->all());
        return $ticket;
    }

    public function destroy($id)
    {
        return response()->json(['success' => ServiceTicket::destroy($id)]);
    }
}