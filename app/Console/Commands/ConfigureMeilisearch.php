<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Meilisearch\Client;

class ConfigureMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:configure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Meilisearch filterable attributes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Configuring Meilisearch filterable attributes...');

        $host = config('scout.meilisearch.host', env('MEILISEARCH_HOST', 'http://localhost:7700'));
        $key = config('scout.meilisearch.key', env('MEILISEARCH_KEY'));

        $client = new Client($host, $key);

        $index = $client->index('products');

        // Set filterable attributes
        $index->updateFilterableAttributes([
            'category_id',
            'brand_id',
            'price',
            'stock',
        ]);

        // Increase max total hits limit (default is 1000)
        // This allows returning more than 1000 results
        $index->updateSettings([
            'pagination' => [
                'maxTotalHits' => 100000, // Allow up to 100,000 results
            ],
        ]);

        $this->info('Filterable attributes configured successfully!');
        $this->info('Filterable: category_id, brand_id, price, stock');
        $this->info('Max total hits limit increased to 100,000');
    }
}
