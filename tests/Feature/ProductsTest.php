<?php

use App\Models\Category;
use App\Models\Products;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('creates a product and syncs categories', function () {
    Storage::fake('public');

    $categories = Category::factory()->count(2)->create();
    $image = UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');

    $response = $this->post('/api/products', [
        'name' => 'Wireless Headphones',
        'price' => '99.99',
        'description' => 'Noise-canceling over-ear headphones.',
        'categories' => $categories->pluck('id')->all(),
        'image' => $image,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('name', 'Wireless Headphones');
    $response->assertJsonPath('price', '99.99');
    $response->assertJsonPath('description', 'Noise-canceling over-ear headphones.');
    expect(Str::startsWith($response->json('image'), 'products/'))->toBeTrue();

    $product = Products::query()->where('name', 'Wireless Headphones')->firstOrFail();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Wireless Headphones',
        'description' => 'Noise-canceling over-ear headphones.',
        'price' => 99.99,
        'image' => $product->image,
    ]);

    foreach ($categories as $category) {
        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);
    }

    Storage::disk('public')->assertExists($product->image);
});
