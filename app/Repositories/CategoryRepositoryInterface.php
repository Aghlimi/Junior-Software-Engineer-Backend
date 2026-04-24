<?php

namespace App\Repositories;

interface CategoryRepositoryInterface
{
    public function all();

    public function create(array $data);

    public function setParent($categoryId, $parentId);
}
