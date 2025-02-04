<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Shipping extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;
    protected $fillable = ['name','price'];

    // Définir les champs traduisibles
    public $translatable = ['name'];
      /**
     * Get all of the orders for the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function orders(): HasMany
    // {
    //     return $this->hasMany(Order::class);
    // }
}


