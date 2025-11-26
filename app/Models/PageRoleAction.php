<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageRoleAction extends Model
{
    use SoftDeletes;
    protected $table = 'page_role_actions';

    // Remove 'page_name' from fillable
    protected $fillable = ['page_code', 'role_id', 'action'];

    public function pageCode()
    {
        return $this->belongsTo(related: Page::class, foreignKey: 'page_code', ownerKey: 'page_code');
    }

    public function role()
    {
        return $this->belongsTo(related: Role::class, foreignKey: 'role_id', ownerKey: 'role_id');
    }
}
