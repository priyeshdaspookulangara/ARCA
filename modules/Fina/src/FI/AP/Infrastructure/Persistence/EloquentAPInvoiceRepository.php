<?php

namespace Modules\Fina\FI\AP\Infrastructure\Persistence;

use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;
use Modules\Fina\FI\AP\Domain\Repositories\APInvoiceRepositoryInterface;

class EloquentAPInvoiceRepository implements APInvoiceRepositoryInterface
{
    public function create(array $data): APInvoiceHeader
    {
        return APInvoiceHeader::create($data);
    }

    public function find(int $id): ?APInvoiceHeader
    {
        return APInvoiceHeader::find($id);
    }

    public function findOpenInvoices(array $criteria): \Illuminate\Support\Collection
    {
        $query = APInvoiceHeader::where('payment_status', 'Open')
            ->whereNull('payment_block')
            ->whereNull('payment_run_id');

        if (isset($criteria['due_date'])) {
            $query->where('due_date', '<=', $criteria['due_date']);
        }

        return $query->get();
    }
}
