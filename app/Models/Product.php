<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ajoutez cette ligne

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'status',
        // 'stock'
    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getStatusOptions(): array
    {
        return [
            'available' => 'Disponible',
            'recommended' => 'Recommandé',
            'seasonal' => 'Saisonnier',
        ];
    }



    public function getFormattedPriceAttribute()
    {
        return '£' . number_format($this->price, 2); // Formate le prix avec 2 décimales
    }

    public function getFormattedPriceWithTextAttribute()
    {
        return '£' . number_format($this->price, 2) . ' | Per Plate'; // Ajoute le texte supplémentaire
    }


}
