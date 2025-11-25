<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    // use SoftDeletes;
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = ['name', 'description', 'parent_id', 'created_by', 'updated_by'];

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

    public function parentId()
    {
        return $this->belongsTo(related: Category::class, foreignKey: 'parent_id');
    }
}
