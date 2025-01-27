<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TranslationSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'api_key',
        'service_id',
        'source_lang',
        'target_lang',
    ];


    protected static function boot()
     {
         parent::boot();

         static::creating(function ($translation) {
             if (Auth::check()) {
                 $translation->user_id = Auth::id();
             }
         });
     }

     public static function getTargetLang($sourceLang)
     {
         return $sourceLang === 'FR' ? 'EN' : ($sourceLang === 'EN' ? 'FR' : null);
     }



    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }


}
