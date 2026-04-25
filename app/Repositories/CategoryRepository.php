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
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return $category;
    }

    public function setParent($categoryId, $parentId)
    {
        $category = Category::find($categoryId);
        if (! $category) {
            return false;
        }

        $category->parent_id = $parentId;
        $category->save();

        return true;
    }

    public function delete($categoryId)
    {
        $category = Category::find($categoryId);
        if (! $category) {
            return false;
        }

        $category->delete();

        return true;
    }
}
