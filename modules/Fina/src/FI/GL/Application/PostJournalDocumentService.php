<?php

namespace Modules\Fina\FI\GL\Application;

use Modules\Fina\FI\GL\Domain\Repositories\JournalDocumentRepositoryInterface;

class PostJournalDocumentService
{
    private $journalDocumentRepository;

    public function __construct(JournalDocumentRepositoryInterface $journalDocumentRepository)
    {
        $this->journalDocumentRepository = $journalDocumentRepository;
    }

    public function __invoke(array $data)
    {
        // Add business logic here, e.g., validation, calculations, etc.
        return $this->journalDocumentRepository->create($data);
    }
}
