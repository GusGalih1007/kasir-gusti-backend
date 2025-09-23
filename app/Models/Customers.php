<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use SoftDeletes;
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = ['first_name','last_name', 'alamat', 'phone', 'email', 'is_member'];
}
