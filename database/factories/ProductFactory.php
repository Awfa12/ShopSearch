<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class;
    public function definition(): array
    {
        // Product name components for variety
        $adjectives = ['Comfortable', 'Stylish', 'Premium', 'Classic', 'Modern', 'Elegant', 'Durable', 'Lightweight'];
        $nouns = ['T-Shirt', 'Shoes', 'Jacket', 'Watch', 'Headphones', 'Laptop', 'Phone', 'Tablet', 'Camera', 'Bag'];
        $colors = ['Red', 'Blue', 'Black', 'White', 'Green', 'Gray', 'Brown', 'Navy'];
        
        $adjective = fake()->randomElement($adjectives);
        $noun = fake()->randomElement($nouns);
        $color = fake()->randomElement($colors);
        $name = "{$adjective} {$color} {$noun}";
        
        // Generate realistic price (between 9.99 and 999.99)
        $price = fake()->randomFloat(2, 9.99, 999.99);
        
        // Generate attributes based on product type
        $attributes = $this->generateAttributes($noun);
        
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->optional(0.8)->paragraph(5),
            'price' => $price,
            'category_id' => Category::factory(), // Creates a category if none exists
            'brand_id' => fake()->optional(0.7)->randomElement([Brand::factory(), null]),
            'attributes' => $attributes,
            'stock' => fake()->numberBetween(0, 500),
            'image_url' => fake()->imageUrl(400, 400, 'products', true, $name),
        ];
    }

    /**
     * Generate realistic attributes based on product type
     */
    private function generateAttributes(string $productType): array
    {
        $attributes = [];
        
        // Common attributes for clothing
        if (in_array($productType, ['T-Shirt', 'Jacket'])) {
            $attributes['color'] = fake()->randomElement(['Red', 'Blue', 'Black', 'White', 'Green', 'Gray']);
            $attributes['size'] = fake()->randomElement(['S', 'M', 'L', 'XL', 'XXL']);
            $attributes['material'] = fake()->randomElement(['Cotton', 'Polyester', 'Wool', 'Leather', 'Denim']);
        }
        
        // Attributes for shoes
        elseif ($productType === 'Shoes') {
            $attributes['color'] = fake()->randomElement(['Black', 'White', 'Brown', 'Navy', 'Gray']);
            $attributes['size'] = fake()->numberBetween(36, 46);
            $attributes['width'] = fake()->randomElement(['Narrow', 'Regular', 'Wide']);
        }
        
        // Attributes for electronics
        elseif (in_array($productType, ['Laptop', 'Phone', 'Tablet', 'Camera'])) {
            $attributes['storage'] = fake()->randomElement(['64GB', '128GB', '256GB', '512GB', '1TB']);
            $attributes['color'] = fake()->randomElement(['Black', 'Silver', 'White', 'Space Gray']);
            if ($productType !== 'Camera') {
                $attributes['screen_size'] = fake()->randomElement(['13"', '14"', '15"', '6.1"', '6.7"']);
            }
        }
        
        // Default attributes for other products
        else {
            $attributes['color'] = fake()->randomElement(['Black', 'White', 'Silver', 'Gray']);
            $attributes['material'] = fake()->randomElement(['Plastic', 'Metal', 'Wood', 'Glass']);
        }
        
        return $attributes;
    }
}
