<?php

namespace Modules\HR\PersonnelAdmin\Domain\Repositories;

interface PersonnelActionRequestRepositoryInterface
{
    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);
}
