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
        'category_id',
        'price_half_litre', 'price_litre'
        // ,'menu_price','extra_price','half_litre_price','litre_price'
    ];

    protected $appends = ['formatted_price_with_text'];


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



    public function category()
    {
        return $this->belongsTo(Category::class); // Un produit appartient à une catégorie
    }



    public function getFormattedPriceAttribute()
    {
        return '£' . number_format($this->price, 2); // Formate le prix avec 2 décimales
    }

    public function getFormattedPriceWithTextAttribute()
    {
        // Mots-clés pour identifier les catégories dynamiquement
        $platKeywords = ['Plat', 'Menu'];
        $boissonKeywords = ['Boisson', 'Naturelles'];

        // Récupérer dynamiquement les slugs des catégories correspondant aux "Plats"
        $slugsPlats = Category::where(function ($query) use ($platKeywords) {
            foreach ($platKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })->pluck('slug')->toArray();

        // Récupérer dynamiquement les slugs des catégories correspondant aux "Boissons"
        $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
            foreach ($boissonKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })->pluck('slug')->toArray();

        // Formater le prix de base
        $formattedPrice = '£' . number_format($this->price, 2);

        // Vérifier si le produit appartient à une catégorie "Menu Complet"
        if (in_array($this->category->slug, $slugsPlats) && $this->category->slug === 'menu-complet') {
            return $formattedPrice . ' | Menu complet (plat + boisson + fruit)';
        }
        // Vérifier si le produit appartient à une catégorie "Plat Seul"
        elseif (in_array($this->category->slug, $slugsPlats) && $this->category->slug === 'plat-seul') {
            return $formattedPrice . ' | Plat seul';
        }
        // Vérifier si le produit appartient à une catégorie "Accompagnements"
        elseif ($this->category->slug === 'accompagnements') {
            return $formattedPrice . ' | Par portion supplémentaire';
        }
        // Vérifier si le produit appartient à une catégorie "Boissons"
        elseif (in_array($this->category->slug, $slugsBoissons)) {
            $halfLitrePrice = '£' . number_format($this->price_half_litre, 2);
            $litrePrice = '£' . number_format($this->price_litre, 2);
            return "$halfLitrePrice le demi-litre | $litrePrice le litre";
        }
        // Retour par défaut pour les autres catégories
        else {
            return $formattedPrice;
        }
    }


}
