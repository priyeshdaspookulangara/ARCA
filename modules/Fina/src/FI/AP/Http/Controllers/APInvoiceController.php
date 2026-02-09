<?php

namespace Modules\Fina\FI\AP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\AP\Application\ProcessVendorInvoiceService;
use Modules\Fina\FI\AP\Domain\Repositories\APInvoiceRepositoryInterface;

class APInvoiceController extends Controller
{
    private $processVendorInvoiceService;
    private $apInvoiceRepository;

    public function __construct(
        ProcessVendorInvoiceService $processVendorInvoiceService,
        APInvoiceRepositoryInterface $apInvoiceRepository
    ) {
        $this->processVendorInvoiceService = $processVendorInvoiceService;
        $this->apInvoiceRepository = $apInvoiceRepository;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $invoice = ($this->processVendorInvoiceService)($data);
        return response()->json($invoice, 201);
    }

    public function show(int $id)
    {
        $invoice = $this->apInvoiceRepository->find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        return response()->json($invoice);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $invoices = $this->apInvoiceRepository->all($status);
        return response()->json($invoices);
    }
}
