<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function all($sort = false, $filter = false, $page = 1, $perPage = 10);

    public function create(array $data);

    public function delete($id);
}
