<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\CustomerMaster\Domain\CustomerRepositoryInterface;
use Modules\CRM\CustomerMaster\Domain\Events\CustomerCreated;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

class CustomerController extends Controller
{
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index()
    {
        return $this->customerRepository->getAll();
    }

    public function store(Request $request)
    {
        $customer = new Customer($request->all());
        $this->customerRepository->save($customer);

        event(new CustomerCreated($customer));

        return $customer;
    }

    public function show($id)
    {
        return $this->customerRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $customer = $this->customerRepository->findById($id);
        $customer->fill($request->all());
        return $this->customerRepository->save($customer);
    }

    public function destroy($id)
    {
        $customer = $this->customerRepository->findById($id);
        return response()->json(['success' => $this->customerRepository->delete($customer)]);
    }
}