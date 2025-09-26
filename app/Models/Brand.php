<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $table = 'brands';
    protected $primaryKey = 'brand_id';

    protected $fillable = ['name', 'description', 'created_by', 'updated_by'];

    public function userCreator()
    {
        return $this->belongsTo(Users::class, 'created_by', 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(Users::class, 'updated_by', 'user_id');
    }
}
