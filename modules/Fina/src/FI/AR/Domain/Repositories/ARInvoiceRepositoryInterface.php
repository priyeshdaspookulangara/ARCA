<?php

namespace Modules\Fina\FI\AR\Domain\Repositories;

use Modules\Fina\FI\AR\Domain\Entities\ARInvoiceHeader;

interface ARInvoiceRepositoryInterface
{
    public function create(array $data): ARInvoiceHeader;
    public function find(int $id): ?ARInvoiceHeader;
}
