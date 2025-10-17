<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\SalesForceAutomation\Domain\TerritoryRepositoryInterface;
use Modules\CRM\SalesForceAutomation\Domain\Model\Territory;

class TerritoryController extends Controller
{
    private $territoryRepository;

    public function __construct(TerritoryRepositoryInterface $territoryRepository)
    {
        $this->territoryRepository = $territoryRepository;
    }

    public function index()
    {
        return $this->territoryRepository->getAll();
    }

    public function store(Request $request)
    {
        $territory = new Territory($request->all());
        return $this->territoryRepository->save($territory);
    }

    public function show($id)
    {
        return $this->territoryRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $territory = $this->territoryRepository->findById($id);
        $territory->fill($request->all());
        return $this->territoryRepository->save($territory);
    }

    public function destroy($id)
    {
        $territory = $this->territoryRepository->findById($id);
        return response()->json(['success' => $this->territoryRepository->delete($territory)]);
    }
}