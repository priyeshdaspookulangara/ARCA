<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Loyalty\Application\LoyaltyService;
use Modules\CRM\CustomerMaster\Domain\CustomerRepositoryInterface;
use Modules\CRM\Loyalty\Domain\Model\LoyaltyProgram;

class LoyaltyController extends Controller
{
    private $loyaltyService;
    private $customerRepository;

    public function __construct(LoyaltyService $loyaltyService, CustomerRepositoryInterface $customerRepository)
    {
        $this->loyaltyService = $loyaltyService;
        $this->customerRepository = $customerRepository;
    }

    public function getBalance(Request $request, $customerId, $programId)
    {
        $customer = $this->customerRepository->findById($customerId);
        $program = LoyaltyProgram::find($programId);
        $balance = $this->loyaltyService->getCustomerBalance($customer, $program);
        return response()->json(['balance' => $balance]);
    }

    public function accrue(Request $request, $customerId, $programId)
    {
        $customer = $this->customerRepository->findById($customerId);
        $program = LoyaltyProgram::find($programId);
        $this->loyaltyService->accruePoints($customer, $program, $request->input('amount'));
        return response()->json(['success' => true]);
    }

    public function redeem(Request $request, $customerId, $programId)
    {
        $customer = $this->customerRepository->findById($customerId);
        $program = LoyaltyProgram::find($programId);
        $this->loyaltyService->redeemPoints($customer, $program, $request->input('points'));
        return response()->json(['success' => true]);
    }
}