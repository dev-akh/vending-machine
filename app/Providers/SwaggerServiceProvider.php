<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Configure Swagger documentation generation
        if (class_exists(\Wotz\LaravelSwaggerUi\SwaggerUi::class)) {
            \Wotz\LaravelSwaggerUi\SwaggerUi::title('Vending Machine API')
                ->description('A comprehensive RESTful API for managing a vending machine system')
                ->version('1.0.0')
                ->contact([
                    'email' => 'support@vending-machine.com',
                    'name' => 'Vending Machine Support'
                ])
                ->license([
                    'name' => 'MIT',
                    'url' => 'https://opensource.org/licenses/MIT'
                ])
                ->servers([
                    [
                        'url' => env('APP_URL') . '/api/v1',
                        'description' => 'Development server'
                    ],
                    [
                        'url' => 'http://api.vending-machine.com/v1',
                        'description' => 'Production server'
                    ]
                ])
                ->securityScheme('bearerAuth', 'http', 'bearer', 'JWT Token')
                ->scan(base_path('app/Http/Controllers/Api'))
                ->generateDocsOn(env('APP_DEBUG', false))
                ->route('/api/documentation');
        }
    }
}
