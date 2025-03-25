<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_designs', function (Blueprint $table) {
            $table->id();
            
            // Stats Section
            $table->boolean('stat_status')->default(true);
            $table->string('stat_product')->nullable();
            $table->string('stat_follower')->nullable();
            $table->string('stat_eval')->nullable();
            
            // Services Section (4 services)
            $table->boolean('service_status')->default(true);
            for($i = 1; $i <= 4; $i++) {
                $table->text('service_pic_' . $i)->nullable();
                $table->string('service_title_' . $i)->nullable();
                $table->text('service_des_' . $i)->nullable();
            }
            
            // Banner Images Section (4 banners)
            $table->boolean('image_banner_status')->default(true);
            for($i = 1; $i <= 4; $i++) {
                $table->text('image_banner_link' . $i)->nullable();
            }
            
            // Effects Section (4 effects)
            $table->boolean('effect_status')->default(true);
            for($i = 1; $i <= 4; $i++) {
                $table->text('effect_pic_' . $i)->nullable();
            }
            
            // Real Reviews Section
            $table->boolean('rep_like_real_status')->default(true);
            $table->text('rep_like_real_pic')->nullable();
            $table->text('rep_like_real_link')->nullable();
            
            // Final Banner Section
            $table->boolean('banner_final_status')->default(true);
            $table->text('banner_final_pic')->nullable();
            
            // Certificate Section
            $table->boolean('cer_status')->default(true);
            $table->string('cer_title')->nullable();
            $table->text('cer_des')->nullable();
            $table->text('cer_link')->nullable();
            $table->text('cer_image')->nullable();
            
            // Video Section
            $table->boolean('video_status')->default(true);
            $table->text('video_link')->nullable();
            
            // About Section
            $table->boolean('about_status')->default(true);
            $table->string('about_title')->nullable();
            $table->text('about_pic')->nullable();
            $table->text('about_des')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_designs');
    }
};