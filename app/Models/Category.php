<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = ['name', 'description', 'parent_id', 'created_by', 'updated_by'];

    public function userCreator()
    {
        return $this->belongsTo(Users::class, 'created_by', 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(Users::class, 'updated_by', 'user_id');
    }
}
