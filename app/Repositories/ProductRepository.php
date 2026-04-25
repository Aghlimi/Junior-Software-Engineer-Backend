<?php

namespace App\Repositories;

use App\Models\Products;

class ProductRepository implements ProductRepositoryInterface
{
    public function all($sort_name = null, $sort_price = null, $category = null, $page = 1, $perPage = 10)
    {
        $products = Products::getProductsByCategory($category);
        if ($sort_price === 'asc' || $sort_price === 'desc') {
            $products = $products->orderBy('price', $sort_price);
        }
        if ($sort_name === 'asc' || $sort_name === 'desc') {
            $products = $products->orderBy('name', $sort_name);
        }

        return $products->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        $product = Products::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'image' => $data['image'],
        ]);

        if (isset($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return $product;
    }

    public function delete($id)
    {
        $product = Products::find($id);
        if (! $product) {
            return false;
        }

        $product->delete();

        return true;
    }
}
