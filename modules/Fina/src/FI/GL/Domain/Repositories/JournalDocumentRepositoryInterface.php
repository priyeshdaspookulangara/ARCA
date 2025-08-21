<?php

namespace Modules\Fina\FI\GL\Domain\Repositories;

use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;

interface JournalDocumentRepositoryInterface
{
    public function create(array $data): GLDocumentHeader;
    public function find(int $id): ?GLDocumentHeader;
    public function list(array $filters): \Illuminate\Database\Eloquent\Collection;
}
