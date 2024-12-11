<?php

namespace App\Observers;

use App\Models\Coupon;

class CouponObserver
{

    /**
     * Handle the Coupon "retrieved" event.
     * Cet événement est déclenché chaque fois qu'un coupon est récupéré depuis la base de données.
     */
    public function retrieved(Coupon $coupon): void
    {
        // Vérifiez si le coupon est expiré et actif, puis mettez à jour son statut
        if ($coupon->expires_at && $coupon->expires_at < now() && $coupon->status === 'active') {
            $coupon->status = 'inactive';
            $coupon->save(); // Sauvegarde des changements
        }
    }


    /**
     * Handle the Coupon "created" event.
     */
    public function created(Coupon $coupon): void
    {
        // Mettre à jour automatiquement le statut après la création
        $coupon->status = $coupon->isValid() ? 'active' : 'inactive';
        $coupon->save();
    }

    /**
     * Handle the Coupon "updated" event.
     */
    public function updated(Coupon $coupon): void
    {
        // Mettre à jour le statut si des modifications ont été apportées
        if ($coupon->expires_at && $coupon->expires_at < now()) {
            $coupon->status = 'inactive';
        } else {
            $coupon->status = 'active';
        }
        $coupon->save();
    }

    /**
     * Handle the Coupon "deleted" event.
     */
    public function deleted(Coupon $coupon): void
    {
        //
    }

    /**
     * Handle the Coupon "restored" event.
     */
    public function restored(Coupon $coupon): void
    {
        //
    }

    /**
     * Handle the Coupon "force deleted" event.
     */
    public function forceDeleted(Coupon $coupon): void
    {
        //
    }
}
