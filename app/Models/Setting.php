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
        'facebook',
        'zalo',
        'logo',
        'app_name',
        'messenger',
        'link_tiktok',
        'bank_number',
        'bank_account_name',
        'bank_name'
    ];
}
