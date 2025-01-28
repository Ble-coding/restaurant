<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    // Définir les champs remplissables
    protected $fillable = [
        // 'title',

        'title_fr',
        'title_en',
        'content_fr',
        'content_en',


        'category_id',
        'slug',
        'status',
        // 'content',
        'image',
    ];

    protected $dates = ['deleted_at'];

    public const STATUSES = [
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'archived' => 'Archivé',
        'pending' => 'En attente',
        'rejected' => 'Rejeté',
    ];

     /**
     * Retourne le statut en français.
     *
     * @return string
     */
    // public function getTranslatedStatus(): string
    // {
    //     return self::STATUSES[$this->status] ?? 'Statut inconnu';
    // }

    public function getTranslatedStatus(): string
    {
        return trans('blog.statuses.' . $this->status);
    }

     /**
     * Vérifie si l'article est publié.
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    // Méthode pour obtenir l'URL complète de l'image (si elle existe)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

     /**
     * Génère un slug unique en fonction du titre.
     *
     * @param string $title
     * @return string
     */
    public static function generateSlug(string $title)
    {
        $slug = Str::slug($title, '-'); // Créer le slug de base

        // Vérifier que le slug est unique
        $count = self::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1); // Ajouter un suffixe pour garantir l'unicité
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(Category::class); // Un blog appartient à une catégorie
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

}
