<?php

namespace App\Console\Commands;

use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

#[Signature('app:create-product-command')]
#[Description('Command description')]
class CreateProductCommand extends Command
{
    function __construct(private ProductService $productService,private CategoryService $categoryService){
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text('name',required: true);
        $price = text('price',required: true);
        $description = text('description',required: true);
        $image = text('image url',required: true);
        $categoriesSet = $this->categoryService->index()->pluck('name', 'id')->mapWithKeys(fn($name, $id) => [$id => $name])->toArray();
        $categories = multiselect(
            label:'Select categories',
            options:$categoriesSet,
            required: true,
            );
        validator([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'image' => $image,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
        ], [
            'name.required' => 'Name is required',
            'price.required' => 'Price is required',
            'description.required' => 'Description is required',
            'image.required' => 'Image URL is required',
        ])->validate();
        $this->productService->create(['name' => $name, 'price' => $price, 'description' => $description, 'image' => $image, 'categories' => $categories]);
        $this->info('Product created successfully');
    }
}
