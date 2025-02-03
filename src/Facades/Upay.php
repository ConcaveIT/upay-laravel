<?php

namespace Concaveit\Upay\Facades;

use Illuminate\Support\Facades\Facade;

class Upay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'upay';
    }
}
