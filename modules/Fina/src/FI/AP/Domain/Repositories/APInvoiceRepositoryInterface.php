<?php

namespace Modules\Fina\FI\AP\Domain\Repositories;

use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;

use Illuminate\Support\Collection;

interface APInvoiceRepositoryInterface
{
    public function create(array $data): APInvoiceHeader;
    public function find(int $id): ?APInvoiceHeader;
    public function findOpenInvoices(array $criteria): Collection;
}
