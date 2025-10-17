<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Sales\Domain\LeadRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\Lead;

class LeadController extends Controller
{
    private $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function index()
    {
        return $this->leadRepository->getAll();
    }

    public function store(Request $request)
    {
        $lead = new Lead($request->all());
        return $this->leadRepository->save($lead);
    }

    public function show($id)
    {
        return $this->leadRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $lead = $this->leadRepository->findById($id);
        $lead->fill($request->all());
        return $this->leadRepository->save($lead);
    }

    public function destroy($id)
    {
        $lead = $this->leadRepository->findById($id);
        return response()->json(['success' => $this->leadRepository->delete($lead)]);
    }
}