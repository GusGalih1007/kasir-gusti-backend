<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username', 
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'role_id',
        'status',
        'last_login',
        'created_by',
        'updated_by'
    ];
    
    public function getJwtIdentifier()
    {
        return $this->getKey();
    }
    public function getJwtCustomClaims()
    {
        return [];
    }
    public function role()
    {
        return $this->belongsTo(related: Role::class, foreignKey: 'role_id', ownerKey: 'role_id');
    }

    public function userCreator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'created_by', ownerKey: 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(related: Users::class, foreignKey: 'updated_by', ownerKey: 'user_id');
    }
}
