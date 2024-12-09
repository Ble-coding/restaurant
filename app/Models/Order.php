<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'address', 'city',
        'zip', 'total', 'status', 'coupon_id', 'order_notes',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
                    ->withPivot('quantity', 'price');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

}
