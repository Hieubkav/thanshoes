<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'brand',
        'type',
    ];

    // product quan hệ nhiều nhiều với cat
    public function cats(){
        return $this->belongsToMany(Cat::class);
    }

    // product quan hệ nhiều nhiều với tag
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    // product quan hệ nhiều nhiều với group 
    public function groups(){
        return $this->belongsToMany(Group::class);
    }

    // một product có nhiều variant 
    public function variants(){
        return $this->hasMany(Variant::class);
    }
}
