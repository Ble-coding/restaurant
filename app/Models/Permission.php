<?php

namespace App\Models;
use Spatie\Permission\Models\Permission as SpatiePermission;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ajoutez cette ligne
// use Spatie\Permission\Models\Role as SpatiePermission;
// use Spatie\Permission\Models\Permission as SpatiePermission;
// extends Model
class Permission extends SpatiePermission
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name', 'translation'
    ];

    protected $dates = ['deleted_at'];

    public static function getResources(): array
    {
        return [
            'users',
            'roles',
            'permissions',
            'coupons',
            'products',
            'orders',
            'menus',
            'blogs',
            'commandes',
            'payments',
            'gateways',
            'shippings',
        ];
    }
}
