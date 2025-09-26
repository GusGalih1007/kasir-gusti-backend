<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';

    protected $fillable = ['name', 'description', 'alamat', 'created_by', 'updated_by'];

    public function userCreator()
    {
        return $this->belongsTo(Users::class, 'created_by', 'user_id');
    }

    public function userUpdator()
    {
        return $this->belongsTo(Users::class, 'updated_by', 'user_id');
    }
}
