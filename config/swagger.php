<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Swagger API Documentation
    |--------------------------------------------------------------------------
    |
    | Configuration for generating Swagger API documentation
    |
    */

    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Vending Machine API',
                'description' => 'A comprehensive RESTful API for managing a vending machine system',
                'version' => '1.0.0',
            ],
            'servers' => [
                [
                    'url' => env('APP_URL') . '/api/v1',
                    'description' => 'Development server',
                ],
            ],
            'info' => [
                'contact' => [
                    'email' => 'support@vending-machine.com',
                ],
                'license' => [
                    'name' => 'MIT',
                    'url' => 'https://opensource.org/licenses/MIT',
                ],
            ],
            'security' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                    'description' => 'JWT token obtained from login endpoint',
                ],
            ],
            'securityDefinitions' => [
                'bearerAuth' => [
                    'type' => 'apiKey',
                    'name' => 'Authorization',
                    'in' => 'header',
                    'description' => 'Enter your bearer token in the format "Bearer {token}"',
                ],
            ],
            'schemes' => ['http', 'https'],
            'consumes' => ['application/json'],
            'produces' => ['application/json'],
            'host' => env('APP_URL'),
            'basePath' => '/api/v1',
        ],
    ],

    'paths' => [
        base_path('app/Http/Controllers/Api'),
    ],

    'excludes' => [
        // Exclude files/directories from scanning
    ],

    'constants' => [
        'L5_SWAGGER_CONST_HOST' => env('APP_URL') . '/api/v1',
    ],

    'parse' => [
        'annotations' => true,
        'imports' => true,
        'analyse' => true,
        'process' => true,
    ],

    'generate_always' => env('APP_DEBUG', false),
    'generate_yaml_copy' => env('APP_DEBUG', false),

    'proxy' => false,

    'additional_config_url' => null,

    'operations_sort' => null,
    'validator_url' => null,

    'headers' => [
        'Accept' => 'application/json',
    ],

    'ui' => [
        'display' => [
            'default_models_expand_depth' => 1,
            'default_model_expand_depth' => 1,
            'display_operation_id' => false,
            'display_request_duration' => false,
            'doc_expansion' => 'none',
            'filter' => true,
            'show_extensions' => false,
            'show_common_extensions' => false,
            'try_it_out_enabled' => true,
            'support_submit_methods' => [
                'get',
                'post',
                'put',
                'delete',
                'patch'
            ],
        ],
        'enabled' => true,
    ],

    'route' => [
        /*
         |--------------------------------------------------------------------------
         | Route for accessing the documentation
         |--------------------------------------------------------------------------
         |
         | Route for accessing the documentation
         |
         */
        'api' => 'api-documentation',
        'docs' => 'api/docs',
        'json' => 'api/docs.json',
    ],
];
