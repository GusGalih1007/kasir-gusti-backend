<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageRoleAction extends Model
{
    use SoftDeletes;
    protected $table = 'page_role_actions';

    protected $fillable = ['page_code', 'page_name', 'role_id', 'action'];

    public function pageCode()
    {
        return $this->belongsTo(Page::class, 'page_code', 'page_code');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
