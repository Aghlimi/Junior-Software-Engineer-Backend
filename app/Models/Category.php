<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'parent_id'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function ParentCategory()
    {
        return $this->belongsTo(CategoryFactory::class, 'parent_id');
    }

    public function ChildrenCategories()
    {
        return $this->hasMany(CategoryFactory::class, 'parent_id');
    }
}
