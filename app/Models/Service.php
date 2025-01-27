<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'identifier',
        'description',
    ];

    /**
     * Relation avec TranslationSetting.
     * Un service peut avoir plusieurs configurations de traduction associées.
     */
    public function translationSettings()
    {
        return $this->hasMany(TranslationSetting::class);
    }


    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
