<?php

namespace App\Services;

use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function index($sort_name = null, $sort_price = null, $category = null, $page = 1, $perPage = 10)
    {
        return $this->productRepository->all($sort_name, $sort_price, $category, $page, $perPage);
    }

    public function create(array $data)
    {
        $product = $this->productRepository->create($data);

        return $product;
    }

    public function delete($id)
    {
        return $this->productRepository->delete($id);
    }
}
