<?php
namespace App\Services;

use App\Repositories\CategoryRepositoryInterface;



class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function index()
    {
        return $this->categoryRepository->all();
    }

    public function create(array $data)
    {
        return $this->categoryRepository->create($data);
    }

    public function setParent($categoryId, $parentId)
    {
        return $this->categoryRepository->setParent($categoryId, $parentId);
    }
}