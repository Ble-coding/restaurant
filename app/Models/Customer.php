<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Utilisez cette classe
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable; // Active les notifications

    // Les champs qui peuvent être remplis en masse
    protected $fillable = ['name', 'email', 'phone', 'password', 'country_code'];

    // Masquer certains attributs, comme le mot de passe et le remember_token
    protected $hidden = ['password', 'remember_token'];
}
