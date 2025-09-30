<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $table = 'roles';
    protected $primaryKey = 'role_id';

    protected $fillable = ['name'];

    public function userRole()
    {
        return $this->hasMany(related: Users::class, foreignKey: 'role_id', localKey: 'role_id');
    }
    public function pageRoleActionRole()
    {
        return $this->hasMany(related: PageRoleAction::class, foreignKey: 'role_id', localKey: 'role_id');
    }
}
