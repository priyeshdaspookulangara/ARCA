<?php

namespace Modules\Fina\FI\AR\Domain\Repositories;

use Modules\Fina\FI\AR\Domain\Entities\ARInvoiceHeader;

use Illuminate\Support\Collection;

interface ARInvoiceRepositoryInterface
{
    public function create(array $data): ARInvoiceHeader;
    public function find(int $id): ?ARInvoiceHeader;
    public function findOverdueInvoicesByCustomerId(int $customerId): Collection;
}
