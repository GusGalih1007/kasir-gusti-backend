<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name',
        'description',
        'slug',
        'price',
        'category_id',
        'brand_id',
        'supplier_id',
        'is_available',
        'photo',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function variant()
    {
        return $this->hasMany(related: ProductVariant::class, foreignKey: 'product_id', localKey: 'product_id');
    }

    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'product_id', 'product_id');
    }

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'updated_by', ownerKey: 'user_id');
    }

    public function brand()
    {
        return $this->belongsTo(related: Brand::class, foreignKey: 'brand_id', ownerKey: 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(related: Category::class, foreignKey: 'category_id', ownerKey: 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(related: Supplier::class, foreignKey: 'supplier_id', ownerKey: 'supplier_id');
    }
}