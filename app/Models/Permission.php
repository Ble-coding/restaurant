<?php

namespace App\Models;
use Spatie\Permission\Models\Permission as SpatiePermission;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ajoutez cette ligne
// use Spatie\Permission\Models\Role as SpatiePermission;
// use Spatie\Permission\Models\Permission as SpatiePermission;
// extends Model
class Permission extends SpatiePermission
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'name',
        // 'guard_name', 'translation'

        'name', // Slug de la permission
        'name_fr', 'name_en', // Noms traduits
        'guard_name',
        'translation_fr', 'translation_en' // Traductions
    ];

    protected $dates = ['deleted_at'];

    public static function getResources(): array
    {
        return [
            'users' => ['en' => 'Users', 'fr' => 'Utilisateurs'],
            'roles' => ['en' => 'Roles', 'fr' => 'Rôles'],
            'categories' => ['en' => 'Catégories', 'fr' => 'Categories'],
            'permissions' => ['en' => 'Permissions', 'fr' => 'Permissions'],
            'coupons' => ['en' => 'Coupons', 'fr' => 'Coupons'],
            'products' => ['en' => 'Products', 'fr' => 'Produits'],
            'orders' => ['en' => 'Orders', 'fr' => 'Commandes'],
            'menus' => ['en' => 'Menus', 'fr' => 'Menus'],
            'blogs' => ['en' => 'Blogs', 'fr' => 'Blogs'],
            'articles' => ['en' => 'Articles', 'fr' => 'Articles'],
            'commandes' => ['en' => 'Orders', 'fr' => 'Commandes'],
            'payments' => ['en' => 'Payments', 'fr' => 'Paiements'],
            'gateways' => ['en' => 'Payment Gateways', 'fr' => 'Passerelles de paiement'],
            'shippings' => ['en' => 'Shippings', 'fr' => 'Livraisons'],
            'translations' => ['en' => 'Translations', 'fr' => 'Traductions'],
            'services' => ['en' => 'Services', 'fr' => 'Services'],
            'settings' => ['en' => 'Settings', 'fr' => 'Paramètres'],
        ];
    }

    /**
     * Retourne les ressources traduites selon la langue actuelle.
     */
    public static function getTranslatedResources(): array
    {
        $locale = app()->getLocale(); // Récupérer la langue actuelle (fr ou en)

        return collect(self::getResources())->mapWithKeys(function ($translations, $key) use ($locale) {
            return [$key => $translations[$locale] ?? $translations['en']]; // Retourne la traduction ou en anglais par défaut
        })->toArray();
    }

}
