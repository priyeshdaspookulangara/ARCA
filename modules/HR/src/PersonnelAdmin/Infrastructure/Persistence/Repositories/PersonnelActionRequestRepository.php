<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Repositories;

use Modules\HR\Models\PersonnelActionRequest;
use Modules\HR\PersonnelAdmin\Domain\Repositories\PersonnelActionRequestRepositoryInterface;

class PersonnelActionRequestRepository implements PersonnelActionRequestRepositoryInterface
{
    public function findById(int $id)
    {
        return PersonnelActionRequest::find($id);
    }

    public function create(array $data)
    {
        return PersonnelActionRequest::create($data);
    }

    public function update(int $id, array $data)
    {
        $request = PersonnelActionRequest::findOrFail($id);
        $request->update($data);
        return $request;
    }
}
