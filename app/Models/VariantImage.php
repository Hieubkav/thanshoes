<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'variant_id',
    ];

    public function variant(){
        return $this->belongsTo(Variant::class);
    }
}
