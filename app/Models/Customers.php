<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    // use SoftDeletes;
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = ['first_name','last_name', 'alamat', 'phone', 'email', 'is_member', 'membership_id', 'created_by', 'updated_by'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'updated_by', ownerKey: 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Membership::class, 'membership_id', 'membership_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }
}
