<?php

namespace Modules\Fina\FI\AR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\AR\Application\DunningService;

class DunningController extends Controller
{
    private DunningService $dunningService;

    public function __construct(DunningService $dunningService)
    {
        $this->dunningService = $dunningService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'run_date' => 'required|date',
        ]);

        $dunnedCustomers = $this->dunningService->runDunning(new \DateTime($data['run_date']));

        return response()->json([
            'message' => 'Dunning run completed successfully.',
            'dunned_customers_count' => count($dunnedCustomers),
            'dunned_customers' => $dunnedCustomers,
        ], 200);
    }
}