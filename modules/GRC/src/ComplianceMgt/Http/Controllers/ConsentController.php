<?php

namespace Modules\GRC\ComplianceMgt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\GRC\ComplianceMgt\Domain\ConsentRepositoryInterface;
use Modules\GRC\ComplianceMgt\Domain\Model\Consent;

class ConsentController extends Controller
{
    private $consentRepository;

    public function __construct(ConsentRepositoryInterface $consentRepository)
    {
        $this->consentRepository = $consentRepository;
    }

    public function index()
    {
        return $this->consentRepository->getAll();
    }

    public function store(Request $request)
    {
        $consent = new Consent($request->all());
        return $this->consentRepository->save($consent);
    }

    public function show($id)
    {
        return $this->consentRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $consent = $this->consentRepository->findById($id);
        $consent->fill($request->all());
        return $this->consentRepository->save($consent);
    }

    public function destroy($id)
    {
        $consent = $this->consentRepository->findById($id);
        return response()->json(['success' => $this->consentRepository->delete($consent)]);
    }

    public function dataRequest(Request $request)
    {
        // In a real implementation, this would handle data access requests (e.g., export, erasure).
        // For now, we will return a success message.
        return response()->json(['status' => 'request_received']);
    }
}