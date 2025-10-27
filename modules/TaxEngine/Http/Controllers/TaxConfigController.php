<?php

namespace Modules\TaxEngine\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TaxEngine\Models\TaxCode;
use Modules\TaxEngine\Models\TaxRate;

class TaxConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TaxCode::with('rates')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $taxCode = TaxCode::updateOrCreate(
            ['code' => $request->input('code')],
            $request->only(['description', 'category', 'status'])
        );

        if ($request->has('rates')) {
            foreach ($request->input('rates') as $rate) {
                $taxCode->rates()->updateOrCreate(
                    ['country' => $rate['country'], 'state' => $rate['state']],
                    $rate
                );
            }
        }

        return $taxCode->load('rates');
    }
}
