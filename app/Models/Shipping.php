<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Shipping extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;
    protected $fillable = [
        'name',
        'price',
        'type',
        'min_price_for_free',
        'conditions'
    ];

       // Casts pour manipuler les données correctement
       protected $casts = [
        'price' => 'decimal:2',
        'min_price_for_free' => 'decimal:2',
        'conditions' => 'array', // JSON stocké en array PHP
    ];


    // Définir les champs traduisibles
    public $translatable = ['name'];

    /**
     * Vérifie si la livraison est gratuite en fonction du type et du montant minimum.
     *
     * @param float $orderTotal Montant total de la commande
     * @return bool
     */
    public function isFree(float $orderTotal): bool
    {
        if ($this->type === 'free') {
            return true;
        }

        if ($this->type === 'conditional' && $this->min_price_for_free !== null) {
            return $orderTotal >= $this->min_price_for_free;
        }

        return false;
    }

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


