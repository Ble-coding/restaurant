<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    const STATUSES = [
        'pending' => 'order.status.pending',
        'preparing' => 'order.status.preparing',
        'shipped' => 'order.status.shipped',
        'delivered' => 'order.status.delivered',
        'canceled' => 'order.status.canceled',
    ];

    // const STATUSES = [
    //     'pending' => ['en' => 'Pending', 'fr' => 'En attente'],
    //     'preparing' => ['en' => 'Preparing', 'fr' => 'En préparation'],
    //     'shipped' => ['en' => 'Shipped', 'fr' => 'Expédié'],
    //     'delivered' => ['en' => 'Delivered', 'fr' => 'Livré'],
    //     'canceled' => ['en' => 'Canceled', 'fr' => 'Annulé'],
    // ];

    public $translatable = ['status'];

    const NON_MODIFIABLE_STATUSES = ['canceled', 'delivered'];


    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'address', 'city','code',
        'zip', 'total', 'status', 'coupon_id', 'order_notes', 'customer_id', 'country_code',
         'payment_id', 'zone_id', 'terms'
         ,'shipping_cost','status_key',
        //  ,'shipping_id'
    ];

    protected $casts = [
        'status' => 'array',

    ];

    public function getStatusInEnglish()
    {
        // Si le statut est stocké sous forme de JSON, on le décode
        if (is_string($this->status) && str_starts_with($this->status, '{')) {
            $statusArray = json_decode($this->status, true);
            return $statusArray['en'] ?? 'unknown';
        }

        // Sinon, on suppose que le statut est déjà une chaîne
        return $this->status;
    }


    public function isCancelable()
    {
        return $this->getStatusInEnglish() === 'pending';
    }



    public function getTranslatedStatusAttribute()
    {
        $locale = App::getLocale(); // Récupère la langue actuelle
        return $this->status[$locale] ?? $this->status['en']; // Fallback en anglais si pas trouvé
    }

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
        return __($this->getStatusTranslationKey());
    }

    // Nouvelle méthode pour récupérer la clé
    public function getStatusTranslationKey()
    {
        return self::STATUSES[$this->status] ?? 'order.status.unknown';
    }
    public function getRawStatus()
    {
        return $this->getAttributes()['status'];
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
