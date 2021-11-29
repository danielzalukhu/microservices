<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
    ];
    
    protected $fillable = [
        'order_id',
        'course_id',
        'price'
    ];

    public function paymentlog()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
