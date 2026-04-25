<?php

namespace App\Console\Commands;

use App\Services\CategoryService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

#[Signature('category:create')]
#[Description('create a category')]
class CreateCategoryCommand extends Command
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
        $name = text('category name', required: true);
        $oldCategories = $this->categoryService->index()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => $name])->toArray();
        $parent = multiselect(
            label: 'Select parent category',
            options: $oldCategories,
            required: false,
            validate: fn ($value) => count($value) > 1 ? 'You can only select one parent category' : null,
        );
        $this->categoryService->create(['name' => $name, 'parent_id' => $parent[0] ?? null]);
        $this->info('Category created successfully');
    }
}
