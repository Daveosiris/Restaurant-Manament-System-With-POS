<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        "product_order_id",
        "product_id",
        "user_id",
        "title",
        "sku",
        "variations",
        "addons",
        "variations_price",
        "addons_price",
        "product_price",
        "total",
        "image",

       ];

       public function product() {
           return $this->belongsTo('App\Models\Product', 'product_id');
       }

}
