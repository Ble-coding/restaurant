<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ajoutez cette ligne
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole // Extends le modèle Role du package Spatie
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name', 'translation'
    ];

    protected $dates = ['deleted_at'];
}
