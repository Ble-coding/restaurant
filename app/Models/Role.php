<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ajoutez cette ligne
use Spatie\Permission\Models\Role as SpatieRole;
// use Spatie\Translatable\HasTranslations;


class Role extends SpatieRole // Extends le modèle Role du package Spatie
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_fr', 'name_en',
        'guard_name',
        'user_id',
        'name',
    ];

     // Champs traduisibles
    //  public $translatable = ['name'];


    protected $dates = ['deleted_at'];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($role) {
    //         if (Auth::check()) {
    //             $role->user_id = Auth::id();
    //         }
    //     });
    // }
}
