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

    public function store(Request $request)
    {
        $data = $request->all();
        $document = ($this->postJournalDocumentService)($data);
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
