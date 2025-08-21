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

    public function list(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = GLDocumentHeader::query();

        if (isset($filters['company_code_id'])) {
            $query->where('company_code_id', $filters['company_code_id']);
        }

        if (isset($filters['posting_date_from'])) {
            $query->where('posting_date', '>=', $filters['posting_date_from']);
        }

        if (isset($filters['posting_date_to'])) {
            $query->where('posting_date', '<=', $filters['posting_date_to']);
        }

        return $query->with('items')->orderBy('posting_date', 'desc')->get();
    }
}
