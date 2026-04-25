<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_name = $request->query('sort_name');
        $sort_price = $request->query('sort_price');
        $category = $request->query('category');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        return $this->productService->index($sort_name, $sort_price, $category, $page, $limit);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['image'] = $request->file('image')->store('products', 'public');

        $product = $this->productService->create($data);

        return response()->json($product, 201);
    }
}
