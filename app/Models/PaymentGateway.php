<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'api_key',
        'site_id',
        'secret_key',
        'callback_url'
    ];


     // Boot method pour générer automatiquement callback_url
     protected static function boot()
     {
         parent::boot();

         static::creating(function ($paymentGateway) {
             if (Auth::check()) {
                 $paymentGateway->user_id = Auth::id();
             }

             // Générer l'URL de retour de paiement
             $paymentGateway->callback_url = url('/payment/cinetpay/callback');
         });
     }


    /**
     * Relation avec le modèle User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

