<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;

class ProductsController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->productService->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            return response()->json(['error' => 'Image is required'], 400);
        }

        $product = $this->productService->create($data);

        if (isset($data['categories'])) {
            $categories = $data['categories'] ?? [];
            $this->productService->setCategories($product->id, $categories);
        }

        return response()->json($product, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->productService->delete($id);

        return response()->noContent();
    }
}
