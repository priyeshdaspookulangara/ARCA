<?php

namespace Modules\Fina\FI\GL\Infrastructure\Persistence;

use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;
use Modules\Fina\FI\GL\Domain\Repositories\JournalDocumentRepositoryInterface;

class EloquentJournalDocumentRepository implements JournalDocumentRepositoryInterface
{
    public function create(array $data): GLDocumentHeader
    {
        $header = GLDocumentHeader::create($data);
        if (isset($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $header->items()->create($itemData);
            }
        }
        return $header;
    }

    public function find(int $id): ?GLDocumentHeader
    {
        return GLDocumentHeader::with('items')->find($id);
    }
}
