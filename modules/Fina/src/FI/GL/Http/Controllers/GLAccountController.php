<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class GLAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $glAccounts = GLAccount::with('chartOfAccount')->get();
        return response()->json($glAccounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'chart_of_accounts_id' => 'required|exists:fina_charts_of_accounts,id',
            'account_number' => [
                'required',
                'max:10',
                Rule::unique('fina_gl_accounts')->where(function ($query) use ($request) {
                    return $query->where('chart_of_accounts_id', $request->chart_of_accounts_id);
                }),
            ],
            'name' => 'required|max:255',
            'account_type' => ['required', Rule::in(['Balance Sheet', 'P&L'])],
            'gl_account_group_id' => 'nullable|exists:fina_gl_account_groups,id',
            'is_reconciliation_account_for' => ['nullable', Rule::in(['Vendor', 'Customer', 'Asset'])],
            'is_open_item_managed' => 'required|boolean',
        ]);

        $glAccount = GLAccount::create($request->all());

        return response()->json($glAccount, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $glAccount = GLAccount::with('chartOfAccount')->findOrFail($id);
        return response()->json($glAccount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $glAccount = GLAccount::findOrFail($id);

        $request->validate([
            'chart_of_accounts_id' => 'required|exists:fina_charts_of_accounts,id',
            'account_number' => [
                'required',
                'max:10',
                Rule::unique('fina_gl_accounts')->where(function ($query) use ($request) {
                    return $query->where('chart_of_accounts_id', $request->chart_of_accounts_id);
                })->ignore($id),
            ],
            'name' => 'required|max:255',
            'account_type' => ['required', Rule::in(['Balance Sheet', 'P&L'])],
            'gl_account_group_id' => 'nullable|exists:fina_gl_account_groups,id',
            'is_reconciliation_account_for' => ['nullable', Rule::in(['Vendor', 'Customer', 'Asset'])],
            'is_open_item_managed' => 'required|boolean',
        ]);

        $glAccount->update($request->all());

        return response()->json($glAccount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        GLAccount::destroy($id);
        return response()->json(null, 204);
    }
}
