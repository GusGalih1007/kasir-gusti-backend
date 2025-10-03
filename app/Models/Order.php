<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = ['user_id', 'customer_id', 'discount', 'order_date', 'status', 'total_amount', 'created_by'];

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }

    public function userId()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'user_id', ownerKey: 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(related: Customers::class, foreignKey: 'customer_id', ownerKey: 'customer_id');
    }

    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }
}
