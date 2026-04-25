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

    $productId = $response->json('id');
    $productImage = $response->json('image');

    $this->assertDatabaseHas('products', [
        'id' => $productId,
        'name' => 'Wireless Headphones',
        'description' => 'Noise-canceling over-ear headphones.',
        'price' => 99.99,
        'image' => $productImage,
    ]);

    foreach ($categories as $category) {
        $this->assertDatabaseHas('category_product', [
            'product_id' => $productId,
            'category_id' => $category->id,
        ]);
    }

    expect(Storage::disk('public')->exists($productImage))->toBeTrue();
});

it('returns validation errors when image is missing', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'No Image Product',
        'price' => '29.99',
        'description' => 'Missing image should fail validation.',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['image']);
});

it('returns validation errors when price is negative', function () {
    $image = UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');

    $response = $this->post('/api/products', [
        'name' => 'Invalid Price Product',
        'price' => '-5',
        'description' => 'Should fail validation.',
        'image' => $image,
    ], [
        'Accept' => 'application/json',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['price']);
});

it('lists products sorted by name and paginated', function () {
    $category = Category::factory()->create();

    $first = Products::factory()->create(['name' => 'Banana', 'price' => 30]);
    $second = Products::factory()->create(['name' => 'Apple', 'price' => 10]);
    $third = Products::factory()->create(['name' => 'Carrot', 'price' => 20]);

    $first->categories()->sync([$category->id]);
    $second->categories()->sync([$category->id]);
    $third->categories()->sync([$category->id]);

    $response = $this->get('/api/products?sort_name=asc&limit=2&page=1');

    $response->assertOk();
    $response->assertJsonPath('current_page', 1);
    $response->assertJsonPath('per_page', 2);
    $response->assertJsonPath('total', 3);
    $response->assertJsonCount(2, 'data');

    $names = collect($response->json('data'))->pluck('name');
    expect($names->values()->all())->toBe(['Apple', 'Banana']);
});

it('filters products by category', function () {
    $categoryA = Category::factory()->create();
    $categoryB = Category::factory()->create();

    $inCategoryA = Products::factory()->count(2)->create();
    $inCategoryB = Products::factory()->count(1)->create();

    $inCategoryA->each(fn (Products $product) => $product->categories()->sync([$categoryA->id]));
    $inCategoryB->each(fn (Products $product) => $product->categories()->sync([$categoryB->id]));

    $response = $this->get('/api/products?category='.$categoryA->id.'&limit=10&page=1');

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id');
    $expectedIds = $inCategoryA->pluck('id');

    expect($ids->sort()->values()->all())->toBe($expectedIds->sort()->values()->all());
});
