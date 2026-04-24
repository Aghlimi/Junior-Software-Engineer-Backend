<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {}
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->categoryService->index();
    }
}
