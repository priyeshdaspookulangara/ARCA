<?php

namespace Modules\Fina\CO\PCA\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PCA\Domain\PcaPostingService;

class PcaPostingController extends Controller
{
    private PcaPostingService $pcaPostingService;

    public function __construct(PcaPostingService $pcaPostingService)
    {
        $this->pcaPostingService = $pcaPostingService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'profit_center_id' => 'required|exists:fina_co_pca_profit_centers,id',
            'gl_account_id' => 'required|exists:fina_gl_accounts,id',
            'document_number' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'posting_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $posting = $this->pcaPostingService->createPosting($data);

        return response()->json($posting, 201);
    }

    public function show($id)
    {
        $posting = $this->pcaPostingService->getPosting($id);

        if (!$posting) {
            return response()->json(['message' => 'Posting not found'], 404);
        }

        return response()->json($posting);
    }

    public function index(Request $request)
    {
        $request->validate([
            'profit_center_id' => 'required|exists:fina_co_pca_profit_centers,id',
        ]);

        $postings = $this->pcaPostingService->getPostingsForProfitCenter($request->profit_center_id);

        return response()->json($postings);
    }

    public function destroy($id)
    {
        $deleted = $this->pcaPostingService->deletePosting($id);

        if (!$deleted) {
            return response()->json(['message' => 'Posting not found'], 404);
        }

        return response()->json(null, 204);
    }
}