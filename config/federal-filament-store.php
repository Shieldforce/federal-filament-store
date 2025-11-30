<?php

use Illuminate\Database\Eloquent\Model;

return [
    'sidebar_group' => 'Loja',
    'modelProducts' => Model::class,
    'getProducts' => [
        'id' => "id",
        'name' => "name",
        'price' => "price",
        'image' => "image",
        'categories' => [
            [
                'id' => "id",
                'name' => "name",
            ]
        ],

    ],
];
