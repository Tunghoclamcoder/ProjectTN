<?php
namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class IndexProducts extends Command
{
    protected $signature = 'elasticsearch:index-products';
    protected $description = 'Index all products to Elasticsearch';

    public function handle()
    {
        $this->info('Đang lập chỉ mục tất cả sản phẩm...');

        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                try {
                    $product->indexToElasticsearch();
                    $this->output->write('.');
                } catch (\Exception $e) {
                    $this->error("Error indexing product {$product->product_id}: {$e->getMessage()}");
                }
            }
        });

        $this->info("\nĐã hoàn tất việc lập chỉ mục sản phẩm!");
    }
}
