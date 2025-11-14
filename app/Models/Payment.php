<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    // use SoftDeletes;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = ['order_id', 'change', 'payment_method', 'amount', 'currency', 'status', 'payment_date', 'created_by'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }
}
