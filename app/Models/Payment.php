<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Payment extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;
    // ;

    // Champs translatables
    public $translatable = ['name'];

    protected $fillable = ['name'];  //ex paiement en ligne , credit bancaire

    /**
     * Get all of the orders for the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paymentGateway(): HasMany
    {
        return $this->hasMany(PaymentGateway::class);
    }
}
