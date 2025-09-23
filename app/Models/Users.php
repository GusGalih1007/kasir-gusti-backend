<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
