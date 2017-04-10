<?php

use Deimos\Providers;

return [

    'poloniex' => [
        'provider' => Providers\Poloniex::class,
        'key'      => '%poloniex.key%',
        'secret'   => '%poloniex.secret%',
    ]

];
