<?php

namespace Shieldforce\FederalFilamentStore\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Cart extends Model
{
    protected $fillable = [
        'identifier',
        'uuid',
        'status',
        'user_id',
    ];

    // hook de inicialização
    protected static function booted()
    {
        static::created(function (Cart $cart) {
            $cart->update([
                'uuid' => Uuid::uuid3(
                    Uuid::NAMESPACE_DNS,
                    (string) $cart->id
                )->toString(),
            ]);
        });
    }
}
