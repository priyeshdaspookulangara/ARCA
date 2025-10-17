<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\SalesForceAutomation\Domain\QuotaRepositoryInterface;
use Modules\CRM\SalesForceAutomation\Domain\Model\Quota;

class QuotaController extends Controller
{
    private $quotaRepository;

    public function __construct(QuotaRepositoryInterface $quotaRepository)
    {
        $this->quotaRepository = $quotaRepository;
    }

    public function index()
    {
        return $this->quotaRepository->getAll();
    }

    public function store(Request $request)
    {
        $quota = new Quota($request->all());
        return $this->quotaRepository->save($quota);
    }

    public function show($id)
    {
        return $this->quotaRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $quota = $this->quotaRepository->findById($id);
        $quota->fill($request->all());
        return $this->quotaRepository->save($quota);
    }

    public function destroy($id)
    {
        $quota = $this->quotaRepository->findById($id);
        return response()->json(['success' => $this->quotaRepository->delete($quota)]);
    }
}