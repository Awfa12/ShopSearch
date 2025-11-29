<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define brand names (same as in BrandFactory)
        $brandNames = [
            'Nike', 'Adidas', 'Samsung', 'Apple', 'Sony', 'LG', 'Canon',
            'HP', 'Dell', 'Lenovo', 'Microsoft', 'Google', 'Amazon',
            'Coca-Cola', 'Pepsi', 'Toyota', 'Honda', 'BMW', 'Mercedes',
            'Zara', 'H&M', 'Gap', 'Levi\'s', 'Puma', 'Under Armour',
            'Philips', 'Bosch', 'Whirlpool', 'KitchenAid', 'Dyson',
            'Nintendo', 'PlayStation', 'Xbox', 'Intel', 'AMD',
            'Nvidia', 'Asus', 'Acer', 'Toshiba', 'Panasonic',
            'Sharp', 'Hisense', 'TCL', 'OnePlus', 'Xiaomi',
            'Huawei', 'Oppo', 'Vivo', 'Realme', 'Motorola'
        ];

        // Create brands using firstOrCreate to avoid duplicates
        foreach ($brandNames as $brandName) {
            Brand::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($brandName)],
                [
                    'name' => $brandName,
                    'description' => fake()->optional()->paragraph(3),
                    'logo_url' => fake()->optional()->imageUrl(200, 200, 'logos'),
                    'active' => true,
                ]
            );
        }

        $this->command->info('Brands seeded successfully!');
    }
}
