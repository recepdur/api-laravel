<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'status',
        'phone',
        'email',
        'tc_no',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }
		
	public function insurances() 
    {
        return $this->hasMany(Insurance::class);
    }  
}
