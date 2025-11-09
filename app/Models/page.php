<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'pages';
    protected $primaryKey = 'page_code';
    protected $fillable = ['page_code', 'page_name', 'action'];
    protected $casts = [
        'action' => 'array',
    ];

    public function pageRoleActionCode()
    {
        return $this->hasMany(related: PageRoleAction::class, foreignKey: 'page_code', localKey: 'page_code');
    }
}
