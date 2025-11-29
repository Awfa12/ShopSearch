<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define top-level categories with their sub-categories
        $categories = [
            'Electronics' => ['Laptops', 'Phones', 'Tablets', 'Cameras', 'Headphones'],
            'Clothing' => ['Men\'s Clothing', 'Women\'s Clothing', 'Kids\' Clothing', 'Accessories'],
            'Home & Garden' => ['Furniture', 'Kitchen', 'Bedding', 'Decor'],
            'Sports & Outdoors' => ['Fitness', 'Camping', 'Cycling', 'Running'],
            'Books' => ['Fiction', 'Non-Fiction', 'Children\'s Books', 'Textbooks'],
            'Toys & Games' => ['Action Figures', 'Board Games', 'Puzzles', 'Video Games'],
        ];

        foreach ($categories as $parentName => $children) {
            // Create parent category (or get existing)
            $parent = Category::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'parent_id' => null,
                    'description' => fake()->optional()->sentence(10),
                    'active' => true,
                ]
            );

            // Create child categories (or get existing)
            foreach ($children as $childName) {
                Category::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($childName)],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'description' => fake()->optional()->sentence(10),
                        'active' => true,
                    ]
                );
            }
        }

        // Create some additional top-level categories without children
        $additionalCategories = [
            'Health & Beauty',
            'Automotive',
            'Food & Beverages',
            'Pet Supplies',
            'Office Supplies',
        ];

        foreach ($additionalCategories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'parent_id' => null,
                    'description' => fake()->optional()->sentence(10),
                    'active' => true,
                ]
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
