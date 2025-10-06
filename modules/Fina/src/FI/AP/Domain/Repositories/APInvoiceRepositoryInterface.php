<?php

namespace Modules\Fina\FI\AP\Domain\Repositories;

use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;

interface APInvoiceRepositoryInterface
{
    public function create(array $data): APInvoiceHeader;
    public function find(int $id): ?APInvoiceHeader;
}
