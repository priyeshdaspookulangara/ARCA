<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Illuminate\Http\JsonResponse;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $chartsOfAccounts = ChartOfAccount::all();
        return response()->json($chartsOfAccounts);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|unique:fina_charts_of_accounts|max:4',
            'name' => 'required|max:255',
            'language_key' => 'required|max:2',
            'length_gl_account_number' => 'required|integer|min:1|max:10',
        ]);

        $chartOfAccount = ChartOfAccount::create($request->all());

        return response()->json($chartOfAccount, 201);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $chartOfAccount = ChartOfAccount::findOrFail($id);
        return response()->json($chartOfAccount);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $chartOfAccount = ChartOfAccount::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:fina_charts_of_accounts,code,' . $id . '|max:4',
            'name' => 'required|max:255',
            'language_key' => 'required|max:2',
            'length_gl_account_number' => 'required|integer|min:1|max:10',
        ]);

        $chartOfAccount->update($request->all());

        return response()->json($chartOfAccount);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        ChartOfAccount::destroy($id);
        return response()->json(null, 204);
    }
}
