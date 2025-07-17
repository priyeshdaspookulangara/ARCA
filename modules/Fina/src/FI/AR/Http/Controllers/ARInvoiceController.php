<?php

namespace Modules\Fina\FI\AR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\AR\Application\ProcessCustomerInvoiceService;
use Modules\Fina\FI\AR\Domain\Repositories\ARInvoiceRepositoryInterface;

class ARInvoiceController extends Controller
{
    private $processCustomerInvoiceService;
    private $arInvoiceRepository;

    public function __construct(
        ProcessCustomerInvoiceService $processCustomerInvoiceService,
        ARInvoiceRepositoryInterface $arInvoiceRepository
    ) {
        $this->processCustomerInvoiceService = $processCustomerInvoiceService;
        $this->arInvoiceRepository = $arInvoiceRepository;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $invoice = ($this->processCustomerInvoiceService)($data);
        return response()->json($invoice, 201);
    }

    public function show(int $id)
    {
        $invoice = $this->arInvoiceRepository->find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        return response()->json($invoice);
    }
}
