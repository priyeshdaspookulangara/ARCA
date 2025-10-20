<?php

namespace Modules\POS\Console;

use Illuminate\Console\Command;
use Modules\POS\Models\ProductCache;
use Modules\POS\Models\LoyaltyCache;
use Illuminate\Support\Facades\Http;

class PosCacheMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:cache-master-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches master data for offline use.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Caching master data...');

        $productResponse = Http::get(config('pos.rth_endpoint') . '/products');
        if ($productResponse->successful()) {
            foreach ($productResponse->json() as $product) {
                ProductCache::updateOrCreate(
                    ['product_id' => $product['id']],
                    [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'tax' => $product['tax'],
                        'last_updated' => now(),
                    ]
                );
            }
        }

        $loyaltyResponse = Http::get(config('pos.rth_endpoint') . '/loyalty');
        if ($loyaltyResponse->successful()) {
            foreach ($loyaltyResponse->json() as $loyalty) {
                LoyaltyCache::updateOrCreate(
                    ['program_id' => $loyalty['id']],
                    [
                        'tier' => $loyalty['tier'],
                        'points_balance' => $loyalty['points'],
                        'last_updated' => now(),
                    ]
                );
            }
        }

        $this->info('Master data cached successfully.');
    }
}
