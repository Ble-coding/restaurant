<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Spatie\Translatable\HasTranslations;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    // , HasTranslations;
    protected $fillable = ['code', 'discount', 'type', 'expires_at', 'status'];

    // Convertir automatiquement expires_at en Carbon
    protected $casts = [
        'expires_at' => 'datetime',
    ];
      // Définir les champs traduisibles
    //   public $translatable = ['description'];

     /**
     * Vérifier si le coupon est valide.
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && (!$this->expires_at || $this->expires_at > now());
    }

    // Formatter expires_at
    public function getFormattedExpiresAtAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('d/m/Y') : 'Illimité';
    }

    // Traduction du type (Montant Fixe ou Pourcentage)
    public function getTranslatedTypeAttribute()
    {
        return $this->type === 'percent' ? __('coupon.percent') : __('coupon.fixed');
    }

    // // Obtenir la description traduite
    // public function getDescriptionAttribute()
    // {
    //     return $this->getTranslation('description', app()->getLocale());
    // }


}
