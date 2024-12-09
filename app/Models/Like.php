<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'blog_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
