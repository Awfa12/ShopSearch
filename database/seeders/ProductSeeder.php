<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing categories and brands
        $categories = Category::pluck('id')->toArray();
        $brands = Brand::pluck('id')->toArray();

        if (empty($categories)) {
            $this->command->error('No categories found! Please run CategorySeeder first.');
            return;
        }

        $this->command->info('Starting to seed 50,000 products...');
        $this->command->info('This may take several minutes...');

        $totalProducts = 50000;
        $chunkSize = 1000; // Insert 1000 products at a time
        $processed = 0;

        // Disable timestamps for faster insertion
        Product::unguard();

        for ($i = 0; $i < $totalProducts; $i += $chunkSize) {
            $products = [];

            for ($j = 0; $j < $chunkSize && ($i + $j) < $totalProducts; $j++) {
                $products[] = $this->generateProductData($categories, $brands);
            }

            // Batch insert
            DB::table('products')->insert($products);

            $processed += count($products);
            $this->command->info("Processed {$processed} / {$totalProducts} products...");
        }

        // Re-enable timestamps
        Product::reguard();

        $this->command->info('Products seeded successfully!');
    }

    
    /**
     * Generate product data for a single product
     */
    private function generateProductData(array $categories, array $brands): array
    {
        $adjectives = ['Comfortable', 'Stylish', 'Premium', 'Classic', 'Modern', 'Elegant', 'Durable', 'Lightweight'];
        $nouns = ['T-Shirt', 'Shoes', 'Jacket', 'Watch', 'Headphones', 'Laptop', 'Phone', 'Tablet', 'Camera', 'Bag'];
        $colors = ['Red', 'Blue', 'Black', 'White', 'Green', 'Gray', 'Brown', 'Navy'];
        
        $adjective = fake()->randomElement($adjectives);
        $noun = fake()->randomElement($nouns);
        $color = fake()->randomElement($colors);
        $name = "{$adjective} {$color} {$noun}";
        
        $price = fake()->randomFloat(2, 9.99, 999.99);
        $attributes = $this->generateAttributes($noun);
        
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 99999),
            'description' => fake()->optional(0.8)->paragraph(5),
            'price' => $price,
            'category_id' => fake()->randomElement($categories),
            'brand_id' => fake()->optional(0.7)->randomElement($brands),
            'attributes' => json_encode($attributes), // Convert array to JSON string
            'stock' => fake()->numberBetween(0, 500),
            'image_url' => fake()->imageUrl(400, 400, 'products', true, $name),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate realistic attributes based on product type
     */
    private function generateAttributes(string $productType): array
    {
        $attributes = [];
        
        if (in_array($productType, ['T-Shirt', 'Jacket'])) {
            $attributes['color'] = fake()->randomElement(['Red', 'Blue', 'Black', 'White', 'Green', 'Gray']);
            $attributes['size'] = fake()->randomElement(['S', 'M', 'L', 'XL', 'XXL']);
            $attributes['material'] = fake()->randomElement(['Cotton', 'Polyester', 'Wool', 'Leather', 'Denim']);
        }
        elseif ($productType === 'Shoes') {
            $attributes['color'] = fake()->randomElement(['Black', 'White', 'Brown', 'Navy', 'Gray']);
            $attributes['size'] = fake()->numberBetween(36, 46);
            $attributes['width'] = fake()->randomElement(['Narrow', 'Regular', 'Wide']);
        }
        elseif (in_array($productType, ['Laptop', 'Phone', 'Tablet', 'Camera'])) {
            $attributes['storage'] = fake()->randomElement(['64GB', '128GB', '256GB', '512GB', '1TB']);
            $attributes['color'] = fake()->randomElement(['Black', 'Silver', 'White', 'Space Gray']);
            if ($productType !== 'Camera') {
                $attributes['screen_size'] = fake()->randomElement(['13"', '14"', '15"', '6.1"', '6.7"']);
            }
        }
        else {
            $attributes['color'] = fake()->randomElement(['Black', 'White', 'Silver', 'Gray']);
            $attributes['material'] = fake()->randomElement(['Plastic', 'Metal', 'Wood', 'Glass']);
        }
        
        return $attributes;
    }
}
