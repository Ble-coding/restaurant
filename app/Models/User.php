<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'country_code',  // Ajout de la colonne country_code
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Relation avec le modèle PaymentGateway.
     *
     * Un utilisateur peut avoir plusieurs passerelles de paiement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function paymentGateways(): HasMany
    {
        return $this->hasMany(PaymentGateway::class);
    }


    // public function hasRole(string $role): bool
    // {
    //     return $this->roles->contains('name', $role);
    // }
    // protected static function booted()
    // {
    //     static::creating(function ($user) {
    //         if (!$user->role_id) {
    //             $user->assignRole('default_role');
    //         }
    //     });
    // }

}
