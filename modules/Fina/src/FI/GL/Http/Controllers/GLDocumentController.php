<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\GL\Application\PostJournalDocumentService;
use Modules\Fina\FI\GL\Domain\Repositories\JournalDocumentRepositoryInterface;

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
}
