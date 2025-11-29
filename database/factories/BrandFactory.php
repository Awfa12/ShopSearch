<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Brand::class;
    public function definition(): array
    {
        $brandNames = [
            'Nike', 'Adidas', 'Samsung', 'Apple', 'Sony', 'LG', 'Canon',
            'HP', 'Dell', 'Lenovo', 'Microsoft', 'Google', 'Amazon',
            'Coca-Cola', 'Pepsi', 'Toyota', 'Honda', 'BMW', 'Mercedes',
            'Zara', 'H&M', 'Gap', 'Levi\'s', 'Puma', 'Under Armour',
            'Philips', 'Bosch', 'Whirlpool', 'KitchenAid', 'Dyson'
        ];

        $name = fake()->randomElement($brandNames);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->optional()->paragraph(3),
            'logo_url' => fake()->optional()->imageUrl(200, 200, 'logos'),
            'active' => true,
        ];
    }
}
