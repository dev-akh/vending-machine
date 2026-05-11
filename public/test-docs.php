<?php

// Simple test to verify documentation access
echo "Swagger Documentation Access Test\n";
echo "==============================\n";

// Check if routes are properly configured
echo "1. Checking if /api-documentation route exists...\n";

// Test basic route registration
try {
    $routes = app('router')->getRoutes();
    $found = false;
    
    foreach ($routes as $route) {
        if ($route->uri() === 'api-documentation') {
            $found = true;
            echo "✓ Route 'api-documentation' found\n";
            echo "  Method: " . implode(', ', $route->methods()) . "\n";
            echo "  URI: " . $route->uri() . "\n";
            break;
        }
    }
    
    if (!$found) {
        echo "✗ Route 'api-documentation' NOT found\n";
        echo "\nAvailable routes:\n";
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'api') !== false) {
                echo "  - " . $route->uri() . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n2. Environment Check...\n";
echo "APP_ENV: " . env('APP_ENV', 'not set') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";

echo "\n3. File Access Test...\n";
$docsPath = public_path('api/docs.json');
if (file_exists($docsPath)) {
    echo "✓ docs.json exists: $docsPath\n";
} else {
    echo "✗ docs.json NOT found: $docsPath\n";
}

echo "\n4. URL Test...\n";
echo "Expected URL: http://localhost:8000/api-documentation\n";
echo "Test this URL in your browser.\n";

echo "\n==============================\n";
