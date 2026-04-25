<?php

namespace App\Console\Commands;

use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

#[Signature('product:create')]
#[Description('create a product')]
class CreateProductCommand extends Command
{
    public function __construct(private ProductService $productService, private CategoryService $categoryService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text('name', required: true);
        $price = text('price', required: true);
        $description = text('description', required: false);
        $image = text('image url', required: true);
        $categoriesSet = $this->categoryService->index()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => $name])->toArray();
        $categories = multiselect(
            label: 'Select categories',
            options: $categoriesSet,
            required: false,
        );
        validator([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'image' => $image,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'string'],
        ], [
            'name.required' => 'Name is required',
            'price.required' => 'Price is required',
            'image.required' => 'Image URL is required',
        ])->validate();

        $this->productService->create([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'image' => $image,
            'categories' => $categories,
        ]);

        $this->info('Product created successfully');
    }
}
