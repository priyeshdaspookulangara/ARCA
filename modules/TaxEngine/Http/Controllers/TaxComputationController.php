<?php

namespace Modules\TaxEngine\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TaxEngine\Services\TaxComputationService;

class TaxComputationController extends Controller
{
    protected $computationService;

    public function __construct(TaxComputationService $computationService)
    {
        $this->computationService = $computationService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tax = $this->computationService->calculate($request->all());

        return response()->json(['tax' => $tax]);
    }
}
