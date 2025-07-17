<?php

namespace Modules\Fina\FI\AR\Application;

use Modules\Fina\FI\AR\Domain\Repositories\ARInvoiceRepositoryInterface;
use Modules\Fina\FI\GL\Application\PostJournalDocumentService;

class ProcessCustomerInvoiceService
{
    private $arInvoiceRepository;
    private $postJournalDocumentService;

    public function __construct(
        ARInvoiceRepositoryInterface $arInvoiceRepository,
        PostJournalDocumentService $postJournalDocumentService
    ) {
        $this->arInvoiceRepository = $arInvoiceRepository;
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
            'document_type' => 'RV', // Customer Invoice
            'transaction_currency_code' => $data['currency'],
            'created_by_user_id' => $data['user_id'],
            'items' => $data['gl_items'], // Should be constructed based on invoice data
        ]);

        // 2. Create the AR invoice header
        $arInvoiceData = array_merge($data, ['gl_document_header_id' => $glDocument->id]);
        $arInvoice = $this->arInvoiceRepository->create($arInvoiceData);

        return $arInvoice;
    }
}
