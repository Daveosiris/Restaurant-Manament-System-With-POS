<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsubCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'language_id', 'category_id', 'status', 'slug'];


    function category()
    {
        return $this->belongsTo(Pcategory::class,  'category_id');
    }

    function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }
}
