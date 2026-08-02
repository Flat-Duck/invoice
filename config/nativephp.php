<?php

use App\Providers\NativeAppServiceProvider;

return [
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),
    'app_id' => env('NATIVEPHP_APP_ID', 'com.invoicepro.desktop'),
    'author' => env('NATIVEPHP_APP_AUTHOR', 'InvoicePro'),
    'copyright' => '© '.date('Y').' InvoicePro',
    'description' => 'Portable offline invoice reporting for desktop.',
    'website' => 'https://localhost',
    'deeplink_scheme' => 'invoicepro',
    'provider' => NativeAppServiceProvider::class,
    'cleanup_env_keys' => ['AWS_*', 'GITHUB_*', '*_SECRET'],
    'cleanup_exclude_files' => ['node_modules', '*/tests', 'storage/app/*.png'],
    'updater' => [
        'enabled' => false,
        'default' => 'spaces',
        'providers' => [
            'spaces' => [
                'driver' => 'spaces',
                'key' => null,
                'secret' => null,
                'name' => null,
                'region' => null,
                'path' => null,
            ],
        ],
    ],
    'queue_workers' => [],
];
