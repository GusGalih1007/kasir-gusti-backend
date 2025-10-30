<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use SoftDeletes;

    protected $table = 'memberships';
    protected $primaryKey = 'membership_id';

    protected $fillable = [
        'membership',
        'benefit',
        'discount',
        'expired_at',
        'created_by',
        'updated_by',
    ];

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'updated_by', ownerKey: 'user_id');
    }

    public function customer()
    {
        return $this->hasMany(Customers::class, 'membership_id', 'membership_id');
    }
}
