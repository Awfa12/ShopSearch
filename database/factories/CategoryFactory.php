<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{

    protected $model = Category::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryNames = [
            'Electronics', 'Clothing', 'Home & Garden', 'Sports & Outdoors',
            'Books', 'Toys & Games', 'Health & Beauty', 'Automotive',
            'Food & Beverages', 'Pet Supplies', 'Office Supplies', 'Jewelry',
            'Musical Instruments', 'Baby Products', 'Fashion Accessories'
        ];

        $name = fake()->randomElement($categoryNames);


        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'parent_id' => null, // handle parent relationships in seeder
            'description' => fake()->optional()->sentence(10),
            'active' => true,
        ];
    }
}
