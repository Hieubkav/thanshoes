<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteDesign extends Model
{
    protected $guarded = [];

    // Cast boolean fields to boolean type
    protected $casts = [
        'stat_status' => 'boolean',
        'service_status' => 'boolean',
        'image_banner_status' => 'boolean',
        'effect_status' => 'boolean',
        'rep_like_real_status' => 'boolean',
        'banner_final_status' => 'boolean',
        'cer_status' => 'boolean',
        'video_status' => 'boolean',
        'about_status' => 'boolean',
    ];
}