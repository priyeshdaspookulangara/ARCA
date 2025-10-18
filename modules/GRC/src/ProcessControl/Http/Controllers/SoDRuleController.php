<?php

namespace Modules\GRC\ProcessControl\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\GRC\ProcessControl\Domain\SoDRuleRepositoryInterface;
use Modules\GRC\ProcessControl\Domain\Model\SoDRule;

class SoDRuleController extends Controller
{
    private $soDRuleRepository;

    public function __construct(SoDRuleRepositoryInterface $soDRuleRepository)
    {
        $this->soDRuleRepository = $soDRuleRepository;
    }

    public function index()
    {
        return $this->soDRuleRepository->getAll();
    }

    public function store(Request $request)
    {
        $soDRule = new SoDRule($request->all());
        return $this->soDRuleRepository->save($soDRule);
    }

    public function show($id)
    {
        return $this->soDRuleRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $soDRule = $this->soDRuleRepository->findById($id);
        $soDRule->fill($request->all());
        return $this->soDRuleRepository->save($soDRule);
    }

    public function destroy($id)
    {
        $soDRule = $this->soDRuleRepository->findById($id);
        return response()->json(['success' => $this->soDRuleRepository->delete($soDRule)]);
    }

    public function checkPolicy(Request $request)
    {
        // In a real implementation, this would evaluate the request against all SoD rules.
        // For now, we will return 'allow'.
        return response()->json(['result' => 'allow']);
    }
}