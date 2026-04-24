<?php

namespace App\Repositories;

use App\Models\Products;

class ProductRepository implements ProductRepositoryInterface
{
    public function all($sort = false, $filter = null, $page = 1, $perPage = 10) {
        $products = Products::getProductsByCategory($filter);
        if ($sort) {
            $products = $products->orderBy('price', $sort);
        }
        return $products->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data) {
        $product = Products::create([
            'name'=> $data['name'],
            'price'=> $data['price'],
            'description'=> $data['description'],
            'image'=> $data['image'],
        ]);
        return $product;
    }
}
