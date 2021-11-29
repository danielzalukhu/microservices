<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'metadata' => 'array'
    ];
    
    protected $fillable = [
        'status',
        'total_price',
        'user_id',    
        'user_name',
        'user_email',
        'snap_url',
        'metadata'
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function paymentlog()
    {
        return $this->hasMany(PaymentLog::class)->orderBy('id', 'ASC');
    }
}
