<?php

namespace Modules\Fina\FI\AP\Application;

use Modules\Fina\FI\AP\Domain\Repositories\APInvoiceRepositoryInterface;
use Modules\Fina\FI\GL\Application\PostJournalDocumentService;

class ProcessVendorInvoiceService
{
    private $apInvoiceRepository;
    private $postJournalDocumentService;

    public function __construct(
        APInvoiceRepositoryInterface $apInvoiceRepository,
        PostJournalDocumentService $postJournalDocumentService
    ) {
        $this->apInvoiceRepository = $apInvoiceRepository;
        $this->postJournalDocumentService = $postJournalDocumentService;
    }

    public function __invoke(array $data)
    {
        // This is a simplified example. A real implementation would have more logic.
        // 1. Create the GL document
        $glDocument = ($this->postJournalDocumentService)([
            'company_code_id' => $data['company_code_id'],
            'document_date' => $data['invoice_date'],
            'posting_date' => $data['invoice_date'],
            'document_type' => 'KR', // Vendor Invoice
            'transaction_currency_code' => $data['currency'],
            'created_by_user_id' => $data['user_id'],
            'items' => $data['gl_items'], // Should be constructed based on invoice data
        ]);

        // 2. Create the AP invoice header
        $apInvoiceData = array_merge($data, ['gl_document_header_id' => $glDocument->id]);
        $apInvoice = $this->apInvoiceRepository->create($apInvoiceData);

        return $apInvoice;
    }
}
