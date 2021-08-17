<?php
/**
 * Created by PhpStorm.
 * User: AbduljeleelNG
 * Date: 8/9/2021
 * Time: 1:04 PM
 */

return[
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users'
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  =>  \App\Models\User::class,
        ]
    ]
];
