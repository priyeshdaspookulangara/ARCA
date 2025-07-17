<?php

namespace Modules\Fina\CO\IO\Domain\Repositories;

use Modules\Fina\CO\IO\Domain\Entities\InternalOrder;

interface InternalOrderRepositoryInterface
{
    public function create(array $data): InternalOrder;
    public function find(int $id): ?InternalOrder;
}
