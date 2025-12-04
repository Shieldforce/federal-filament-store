<?php

use Illuminate\Database\Eloquent\Model;

return [
    'sidebar_group'       => 'Loja',
    'products_callback'   => [],
    'categories_callback' => [],
    'user_callback'       => Model::class,
    'order_callback'      => Model::class,
];
