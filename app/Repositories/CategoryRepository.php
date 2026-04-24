<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        $categories = Category::all();
        return $categories;
    }

    public function create(array $data)
    {
        $category = Category::create([
            'name' => $data['name'],
        ]);
        return $category;
    }

    public function setParent($categoryId, $parentId)
    {
        $category = Category::find($categoryId);
        $category->parent_id = $parentId;
        $category->save();
    }
}
