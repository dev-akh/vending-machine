<?php

// Simple route debugging
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Create a simple request to test routes
$request = Illuminate\Http\Request::create('/api-documentation', 'GET');

try {
    $response = $app->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "✓ SUCCESS: /api-documentation is accessible!\n";
    } else {
        echo "✗ FAILED: /api-documentation returned " . $response->getStatusCode() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Route Registration Test ===\n";

// Test raw route registration
use Illuminate\Support\Facades\Route;

Route::get('/debug-routes', function () {
    echo "<h1>Route Debug Information</h1>";
    echo "<h2>Registered Routes:</h2>";
    echo "<pre>";
    
    $routes = app('router')->getRoutes();
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'api') !== false) {
            echo $route->uri() . " -> " . implode(', ', $route->methods()) . "\n";
        }
    }
    
    echo "</pre>";
    
    echo "<h2>Environment:</h2>";
    echo "<pre>";
    echo "APP_ENV: " . env('APP_ENV', 'not set') . "\n";
    echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
    echo "</pre>";
    
    echo "<h2>Test Links:</h2>";
    echo '<a href="/api-documentation" target="_blank">Test /api-documentation</a><br>';
    echo '<a href="/api/docs" target="_blank">Test /api/docs</a><br>';
    echo '<a href="/api/docs.json" target="_blank">Test /api/docs.json</a><br>';
});
