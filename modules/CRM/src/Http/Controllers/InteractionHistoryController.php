<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Sales\Domain\InteractionHistoryRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\InteractionHistory;

class InteractionHistoryController extends Controller
{
    private $interactionHistoryRepository;

    public function __construct(InteractionHistoryRepositoryInterface $interactionHistoryRepository)
    {
        $this->interactionHistoryRepository = $interactionHistoryRepository;
    }

    public function index()
    {
        return $this->interactionHistoryRepository->getAll();
    }

    public function store(Request $request)
    {
        $interactionHistory = new InteractionHistory($request->all());
        return $this->interactionHistoryRepository->save($interactionHistory);
    }

    public function show($id)
    {
        return $this->interactionHistoryRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $interactionHistory = $this->interactionHistoryRepository->findById($id);
        $interactionHistory->fill($request->all());
        return $this->interactionHistoryRepository->save($interactionHistory);
    }

    public function destroy($id)
    {
        $interactionHistory = $this->interactionHistoryRepository->findById($id);
        return response()->json(['success' => $this->interactionHistoryRepository->delete($interactionHistory)]);
    }
}