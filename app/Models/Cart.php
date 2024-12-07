<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ajoutez cette ligne

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['session_id', 'is_active'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
