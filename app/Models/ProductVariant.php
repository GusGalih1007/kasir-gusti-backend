<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    // use SoftDeletes;

    protected $table = 'product_variants';
    protected $primaryKey = 'variant_id';

    protected $fillable = ['product_id', 'variant_name', 'price', 'sku', 'stock_qty', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'updated_by', ownerKey: 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(related: Product::class, foreignKey: 'product_id', ownerKey: 'product_id');
    }
}
