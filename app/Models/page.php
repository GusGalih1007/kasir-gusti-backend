<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'pages';
    protected $primaryKey = 'page_code';
    protected $fillable = ['page_name', 'action'];

    public function pageRoleActionCode()
    {
        return $this->hasMany(PageRoleAction::class, 'page_code', 'page_code');
    }
}
