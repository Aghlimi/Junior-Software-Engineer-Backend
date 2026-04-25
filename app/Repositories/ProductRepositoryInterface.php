<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function all($name = null, $price = null, $category = null, $page = 1, $perPage = 10);

    public function create(array $data);

    public function delete($id);
}
