<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Fina\FI\GL\Domain\Entities\RecurringEntryDocument;
use Modules\Fina\FI\GL\Application\RunRecurringEntriesService;
use Carbon\Carbon;

class RecurringEntryController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = RecurringEntryDocument::with('items')->get();
        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'required|integer|exists:fina_company_codes,id',
            'document_type' => 'required|string|max:4',
            'transaction_currency_code' => 'required|string|max:3',
            'header_text' => 'nullable|string',
            'frequency' => ['required', Rule::in(['MONTHLY', 'QUARTERLY', 'YEARLY'])],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'items' => 'required|array|min:2',
            'items.*.gl_account_id' => 'required|integer|exists:fina_gl_accounts,id',
            'items.*.posting_type' => ['required', Rule::in(['Debit', 'Credit'])],
            'items.*.amount_transaction_currency' => 'required|numeric|min:0.01',
            'items.*.item_text' => 'nullable|string',
        ]);

        // TODO: Validate that debits equal credits

        $document = DB::transaction(function () use ($validated) {
            $headerData = collect($validated)->except('items')->toArray();
            $headerData['next_run_date'] = $headerData['start_date'];

            $doc = RecurringEntryDocument::create($headerData);
            $doc->items()->createMany($validated['items']);
            return $doc;
        });

        return response()->json($document->load('items'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $document = RecurringEntryDocument::with('items')->findOrFail($id);
        return response()->json($document);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $document = RecurringEntryDocument::findOrFail($id);

        $validated = $request->validate([
            // Validation rules similar to store
            'header_text' => 'sometimes|required|string',
            'frequency' => ['sometimes','required', Rule::in(['MONTHLY', 'QUARTERLY', 'YEARLY'])],
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'items' => 'sometimes|required|array|min:2',
            // ... etc
        ]);

        DB::transaction(function () use ($document, $validated) {
            $headerData = collect($validated)->except('items')->toArray();
            $document->update($headerData);

            if (isset($validated['items'])) {
                $document->items()->delete();
                $document->items()->createMany($validated['items']);
            }
        });

        return response()->json($document->load('items'));
    }

    public function destroy(int $id): JsonResponse
    {
        RecurringEntryDocument::destroy($id);
        return response()->json(null, 204);
    }

    public function run(Request $request, RunRecurringEntriesService $runService): JsonResponse
    {
        $validated = $request->validate([
            'run_date' => 'nullable|date',
        ]);

        $runDate = isset($validated['run_date']) ? Carbon::parse($validated['run_date']) : Carbon::now();

        $results = $runService->handle($runDate);

        return response()->json($results);
    }
}
