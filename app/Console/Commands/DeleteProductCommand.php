<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

#[Signature('product:delete')]
#[Description('delete a product')]
class DeleteProductCommand extends Command
{
    public function __construct(private ProductService $productService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = $this->productService->index()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => $name])->toArray();
        $selectedProduct = select(
            label: 'Select a product to delete',
            options: $products
        );
        if ($selectedProduct) {
            $this->productService->delete($selectedProduct);
            $this->info('Product deleted successfully');
        }
    }
}
