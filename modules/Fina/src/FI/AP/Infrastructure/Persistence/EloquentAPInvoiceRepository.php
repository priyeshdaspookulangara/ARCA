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
}
