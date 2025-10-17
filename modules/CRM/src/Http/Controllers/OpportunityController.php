<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Sales\Domain\OpportunityRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\Opportunity;

class OpportunityController extends Controller
{
    private $opportunityRepository;

    public function __construct(OpportunityRepositoryInterface $opportunityRepository)
    {
        $this->opportunityRepository = $opportunityRepository;
    }

    public function index()
    {
        return $this->opportunityRepository->getAll();
    }

    public function store(Request $request)
    {
        $opportunity = new Opportunity($request->all());
        return $this->opportunityRepository->save($opportunity);
    }

    public function show($id)
    {
        return $this->opportunityRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $opportunity = $this->opportunityRepository->findById($id);
        $opportunity->fill($request->all());
        return $this->opportunityRepository->save($opportunity);
    }

    public function destroy($id)
    {
        $opportunity = $this->opportunityRepository->findById($id);
        return response()->json(['success' => $this->opportunityRepository->delete($opportunity)]);
    }
}