<?php

namespace Modules\Fina\FI\AR\Infrastructure\Persistence;

use Modules\Fina\FI\AR\Domain\Entities\ARInvoiceHeader;
use Modules\Fina\FI\AR\Domain\Repositories\ARInvoiceRepositoryInterface;

class EloquentARInvoiceRepository implements ARInvoiceRepositoryInterface
{
    public function create(array $data): ARInvoiceHeader
    {
        return ARInvoiceHeader::create($data);
    }

    public function find(int $id): ?ARInvoiceHeader
    {
        return ARInvoiceHeader::find($id);
    }
}
