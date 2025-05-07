<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateProductSlugs extends Command
{
    protected $signature = 'products:generate-slugs';
    protected $description = 'Tạo slug cho tất cả sản phẩm từ tên sản phẩm';

    public function handle()
    {
        $products = Product::whereNull('slug')->orWhere('slug', '')->get();

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        foreach ($products as $product) {
            $slug = Str::slug($product->name);
            $originalSlug = $slug;
            $counter = 1;

            // Kiểm tra xem slug đã tồn tại chưa
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $product->slug = $slug;
            $product->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Đã tạo slug cho ' . count($products) . ' sản phẩm!');
    }
}
