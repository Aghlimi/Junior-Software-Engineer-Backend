<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;

/**
 *  get all categories
 */
class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->categoryService->index();
    }
}
