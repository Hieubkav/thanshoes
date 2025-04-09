<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'email',
        'address',
        'slogan',
        'facebook',
        'zalo',
        'logo',
        'app_name',
        'messenger',
        'link_tiktok',
        'bank_number',
        'bank_account_name',
        'bank_name',
        'ban_name_product_one',
        'ban_name_product_two',
        'ban_name_product_three',
        'ban_name_product_four',
        'ban_name_product_five',
        'size_shoes_image',
        'dec_product_price',
        'round_price',
        'apply_price',
    ];
}
