<?php

namespace App\Console\Commands;

use App\Services\CategoryService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

#[Signature('category:delete')]
#[Description('Delete a category')]
class DeleteCategoryCommand extends Command
{
    public function __construct(protected CategoryService $categoryService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = $this->categoryService->index()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => $name])->toArray();
        $category = select(
            label: 'Select parent category',
            options: $categories
        );

        $this->categoryService->delete($category);
        $this->info('Category deleted successfully');
    }
}
