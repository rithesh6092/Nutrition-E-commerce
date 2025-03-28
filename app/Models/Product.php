<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_id', 'status', 'stock', 'mrp', 'weight', 'svp_points', 'image_url'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('rating', 5);
    }
}
