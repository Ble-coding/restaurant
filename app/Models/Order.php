<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const STATUSES = [
        'pending' => 'En attente',
        'preparing' => 'En cours de préparation',
        'shipped' => 'Expédiée',
        'delivered' => 'Livrée',
        'canceled' => 'Annulée',
    ];

    const NON_MODIFIABLE_STATUSES = ['canceled', 'delivered'];


    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'address', 'city','code',
        'zip', 'total', 'status', 'coupon_id', 'order_notes', 'customer_id', 'country_code',
         'payment_id', 'zone_id', 'terms'
         ,'shipping_cost'
        //  ,'shipping_id'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
                    ->withPivot('quantity', 'price','size');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relation avec le modèle Payment
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    // Relation avec le modèle Zone
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    // Méthode pour récupérer l'étiquette du statut
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? 'Statut inconnu';
    }


    // Relation avec le modèle Shipping
    // public function shipping()
    // {
    //     return $this->belongsTo(shipping::class);
    // }

     // Générateur de code
     public static function generateOrderCode()
     {
         do {
             $code = '#95' . rand(1000, 9999); // Générer un code commençant par 95
         } while (self::where('code', $code)->exists());

         return $code;
     }

    // Historique des modifications
    public function orderLogs()
    {
        return $this->hasMany(OrderLog::class);
    }


}
