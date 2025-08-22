<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\GL\Application\PostJournalDocumentService;
use Modules\Fina\FI\GL\Application\ReverseJournalDocumentService;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;
use Modules\Fina\FI\GL\Domain\Repositories\JournalDocumentRepositoryInterface;
use Carbon\Carbon;

class GLDocumentController extends Controller
{
    private $postJournalDocumentService;
    private $journalDocumentRepository;

    public function __construct(
        PostJournalDocumentService $postJournalDocumentService,
        JournalDocumentRepositoryInterface $journalDocumentRepository
    ) {
        $this->postJournalDocumentService = $postJournalDocumentService;
        $this->journalDocumentRepository = $journalDocumentRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'sometimes|integer|exists:fina_company_codes,id',
            'posting_date_from' => 'sometimes|date',
            'posting_date_to' => 'sometimes|date|after_or_equal:posting_date_from',
        ]);

        $documents = $this->journalDocumentRepository->list($validated);

        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'required|integer|exists:fina_company_codes,id',
            'document_date' => 'required|date',
            'posting_date' => 'required|date',
            'document_type' => 'required|string|max:4',
            'transaction_currency_code' => 'required|string|max:3',
            'reference_text' => 'nullable|string|max:255',
            'header_text' => 'nullable|string|max:255',
            'is_reversing_entry' => 'sometimes|boolean',
            'reverses_on_date' => 'required_if:is_reversing_entry,true|date|after:posting_date',
            'items' => 'required|array|min:2',
            'items.*.gl_account_id' => 'required|integer|exists:fina_gl_accounts,id',
            'items.*.posting_type' => ['required', \Illuminate\Validation\Rule::in(['Debit', 'Credit'])],
            'items.*.amount_transaction_currency' => 'required|numeric|min:0.01',
            'items.*.item_text' => 'nullable|string',
        ]);

        // TODO: Add validation for balanced document (debits === credits)

        $document = ($this->postJournalDocumentService)($validated);

        return response()->json($document, 201);
    }

    public function show(int $id)
    {
        $document = $this->journalDocumentRepository->find($id);
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        return response()->json($document);
    }

    /**
     * Reverse the specified document.
     * @param Request $request
     * @param int $id
     * @param ReverseJournalDocumentService $reversalService
     * @return JsonResponse
     */
    public function reverse(Request $request, int $id, ReverseJournalDocumentService $reversalService): JsonResponse
    {
        $request->validate([
            'reversal_reason' => 'required|string|max:10',
            'reversal_date' => 'required|date',
        ]);

        try {
            $originalDocument = $this->journalDocumentRepository->find($id);
            if (!$originalDocument) {
                return response()->json(['message' => 'Document not found'], 404);
            }

            $reversalDate = Carbon::parse($request->input('reversal_date'));

            $reversalDocument = $reversalService->handle(
                $originalDocument,
                $request->input('reversal_reason'),
                $reversalDate
            );

            return response()->json($reversalDocument->load('items'), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }
}
