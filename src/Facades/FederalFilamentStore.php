<?php

namespace Shieldforce\FederalFilamentStore\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Shieldforce\FederalFilamentStore\FederalFilamentStore
 */
class FederalFilamentStore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Shieldforce\FederalFilamentStore\FederalFilamentStore::class;
    }
}
