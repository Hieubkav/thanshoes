<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // Thêm trang chủ
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency('daily')
            ->setPriority(1.0));

        // Thêm trang danh sách bài viết
        $sitemap->add(Url::create('/posts')
            ->setLastModificationDate(now())
            ->setChangeFrequency('daily')
            ->setPriority(0.8));

        // Thêm các trang sản phẩm
        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            $sitemap->add(Url::create("/product/{$product->slug}")
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.7));
        }

        // Thêm các trang bài viết
        $posts = \App\Models\Post::all();
        foreach ($posts as $post) {
            $sitemap->add(Url::create("/posts/{$post->id}")
                ->setLastModificationDate($post->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.6));
        }

        return $sitemap->render();
    }
}