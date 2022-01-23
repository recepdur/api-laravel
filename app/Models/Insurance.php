<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'status',
        'start_date',
        'end_date',
        'plate_no',
        'car_register_no',
        'company',
        'policy_no',
        'description',
        'commission_rate',
        'gross_price',
        'net_price',
        'commission_price',
    ];

    public function customer() 
    {
        return $this->belongsTo(Customer::class);
    }
}
