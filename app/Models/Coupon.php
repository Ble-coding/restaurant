<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['code', 'discount', 'type', 'expires_at', 'status'];

    // Convertir automatiquement expires_at en Carbon
    protected $casts = [
        'expires_at' => 'datetime',
    ];


      // Vérifier si le coupon est valide
      public function isValid(): bool
      {
          return $this->status === 'active' && (!$this->expires_at || $this->expires_at > now());
      }

      // Formatter expires_at
      public function getFormattedExpiresAtAttribute()
      {
          return $this->expires_at ? $this->expires_at->format('d/m/Y') : 'Illimité';
      }

      public function getTranslatedTypeAttribute()
    {
        return $this->type === 'PERCENT' ? 'Pourcentage' : 'Montant fixe';
    }



}
