<?php

namespace Modules\Fina\FI\AP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\AP\Application\AutomaticPaymentService;
use Modules\Fina\FI\AP\Domain\Repositories\PaymentRunRepository;

class AutomaticPaymentController extends Controller
{
    private AutomaticPaymentService $automaticPaymentService;
    private PaymentRunRepository $paymentRunRepository;

    public function __construct(
        AutomaticPaymentService $automaticPaymentService,
        PaymentRunRepository $paymentRunRepository
    ) {
        $this->automaticPaymentService = $automaticPaymentService;
        $this->paymentRunRepository = $paymentRunRepository;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'run_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        $paymentRun = $this->automaticPaymentService->createPaymentProposal($data);

        return response()->json($paymentRun, 201);
    }

    public function show($id)
    {
        $paymentRun = $this->paymentRunRepository->findById($id);

        if (!$paymentRun) {
            return response()->json(['message' => 'Payment run not found'], 404);
        }

        $paymentRun->load('proposals.invoice');

        return response()->json($paymentRun);
    }

    public function update(Request $request, $id)
    {
        $this->automaticPaymentService->executePaymentRun($id);

        return response()->json(['message' => 'Payment run executed successfully.']);
    }

    public function destroy($id)
    {
        $this->automaticPaymentService->cancelPaymentRun($id);

        return response()->json(null, 204);
    }
}