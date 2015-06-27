<?php

/*
 * This file is part of Laravel Flysystem.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'awss3',

    /*
    |--------------------------------------------------------------------------
    | Flysystem Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Examples of
    | configuring each supported driver is shown below. You can of course have
    | multiple connections per driver.
    |
    */

    'connections' => [
        'awss3' => [
            'driver'  => 'awss3',
            'key'     => env('S3_KEY'),
            'secret'  => env('S3_SECRET'),
            'bucket'  => env('S3_BUCKET'),
            'region'  => env('S3_REGION', 'us-east-1'),
            'version' => 'latest',
            'cache'   => 'default'
            // 'bucket_endpoint' => false,
            // 'calculate_md5'   => true,
            // 'scheme'          => 'https',
            // 'endpoint'        => 'your-url',
            // 'prefix'          => 'your-prefix',
            // 'visibility'      => 'public',
            // 'eventable'       => true,
        ],
        'local' => [
            'driver'     => 'local',
            'path'       => storage_path('files')
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Flysystem Cache
    |--------------------------------------------------------------------------
    |
    | Here are each of the cache configurations setup for your application.
    | There are currently two drivers: illuminate and adapter. Examples of
    | configuration are included. You can of course have multiple connections
    | per driver as shown.
    |
    */

    'cache' => [
        'default' => [
            'driver'    => 'illuminate',
            'connector' => null, // null means use default driver
            'key'       => env('FLYSYSTEM_CACHE_KEY', 'flysystem')
        ],
    ],
];
