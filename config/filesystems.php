<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY','AKIAUNSBEHXHQEBJKOG7'),
            'secret' => env('AWS_SECRET','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'),
            'region' => env('AWS_REGION','us-east-2'),
            'bucket' => env('AWS_BUCKET', 'tsicdev'),
        ],

        's3DBBucket' => [
            'driver' => 's3',
            'key' => 'AKIAUNSBEHXHQEBJKOG7',
            'secret' => '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',
            'region' => 'us-east-2',
            'bucket' => '17dat5a86baa4ea5dtta',
        ],

    ],

];