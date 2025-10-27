<?php

namespace Modules\TaxEngine\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TaxEngine\Services\TaxReconciliationService;

class TaxReconciliationController extends Controller
{
    protected $reconciliationService;

    public function __construct(TaxReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    public function store(Request $request)
    {
        $result = $this->reconciliationService->reconcile();

        return response()->json($result);
    }
}
