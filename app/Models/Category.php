<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, SoftDeletes
    , HasTranslations;

    // Champs traduisibles
    public $translatable = ['name', 'slug'];
    protected $fillable = ['name', 'slug'];


    public function blogs()
    {
        return $this->hasMany(Blog::class); // Une catégorie a plusieurs blogs
    }

    public function products()
    {
        return $this->hasMany(Product::class); // Une catégorie a plusieurs products
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($category) {
    //         $category->slug = Str::slug($category->getTranslation('name', app()->getLocale()));
    //     });

    //     static::updating(function ($category) {
    //         $category->slug = Str::slug($category->getTranslation('name', app()->getLocale()));
    //     });
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $translations = $category->getTranslations('name');

            $slugs = [];
            foreach ($translations as $lang => $name) {
                if (!$category->hasTranslation('slug', $lang)) {
                    $slugs[$lang] = Str::slug($name);
                }
            }

            $category->setTranslations('slug', $slugs);
        });

        static::updating(function ($category) {
            $translations = $category->getTranslations('name');

            $slugs = [];
            foreach ($translations as $lang => $name) {
                if (!$category->hasTranslation('slug', $lang)) {
                    $slugs[$lang] = Str::slug($name);
                }
            }

            $category->setTranslations('slug', $slugs);
        });
    }

}
