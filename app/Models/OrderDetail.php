<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    // use SoftDeletes;

    protected $table = 'order_details';
    protected $primaryKey = 'order_detail_id';

    protected $fillable = ['order_id', 'product_id', 'variant_id', 'quantity', 'price_at_purchase', 'total_price'];

    public function product()
    {
        return $this->belongsTo(related: Product::class, foreignKey: 'product_id', ownerKey: 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(related: Order::class, foreignKey: 'order_id', ownerKey: 'order_id');
    }

    public function variant()
    {
        return $this->belongsTo(related: ProductVariant::class, foreignKey: 'variant_id', ownerKey: 'variant_id');
    }
}
