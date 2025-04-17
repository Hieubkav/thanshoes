<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    // tag quan hệ nhiều nhiều với product
    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
