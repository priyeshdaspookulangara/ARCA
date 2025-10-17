<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Marketing\Application\CampaignService;
use Modules\CRM\Marketing\Domain\Model\Campaign;

class CampaignController extends Controller
{
    private $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index()
    {
        return Campaign::all();
    }

    public function store(Request $request)
    {
        $campaign = Campaign::create($request->all());
        return $campaign;
    }

    public function show($id)
    {
        return Campaign::find($id);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);
        $campaign->update($request->all());
        return $campaign;
    }

    public function destroy($id)
    {
        return response()->json(['success' => Campaign::destroy($id)]);
    }
}