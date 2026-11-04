<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $middlewareAliases = [
        'auth' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'role' => \App\Middleware\RoleMiddleware::class,
    ];

    /**
     * Register a GET route
     */
    public static function get(string $uri, callable|array|string $action, array $middlewares = []): int
    {
        return self::addRoute('GET', $uri, $action, $middlewares);
    }

    /**
     * Register a POST route
     */
    public static function post(string $uri, callable|array|string $action, array $middlewares = []): int
    {
        return self::addRoute('POST', $uri, $action, $middlewares);
    }

    /**
     * Attach middleware to the most recently registered route
     */
    public static function middleware(string|array $middlewares): void
    {
        $lastIndex = count(self::$routes) - 1;
        if ($lastIndex >= 0) {
            $list = is_array($middlewares) ? $middlewares : [$middlewares];
            self::$routes[$lastIndex]['middlewares'] = array_merge(self::$routes[$lastIndex]['middlewares'], $list);
        }
    }

    /**
     * Add route definition to registry
     */
    private static function addRoute(string $method, string $uri, callable|array|string $action, array $middlewares = []): int
    {
        $uri = '/' . trim($uri, '/');
        self::$routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'action' => $action,
            'middlewares' => $middlewares,
        ];
        return count(self::$routes) - 1;
    }

    /**
     * Resolve incoming HTTP request and dispatch to action
     */
    public static function dispatch(string $requestMethod, string $requestUri): mixed
    {
        $method = strtoupper($requestMethod);
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $parsedUrl = parse_url($requestUri, PHP_URL_PATH) ?? '/';

        // Strip subfolder prefix if running under localhost/camhub/public
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && str_starts_with($parsedUrl, $scriptName)) {
            $parsedUrl = substr($parsedUrl, strlen($scriptName));
        }

        $parsedUrl = '/' . trim($parsedUrl, '/');

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Convert route pattern with parameters to regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $parsedUrl, $matches)) {
                // Execute Middlewares first
                if (!empty($route['middlewares'])) {
                    foreach ($route['middlewares'] as $middlewareDef) {
                        $parts = explode(':', $middlewareDef, 2);
                        $alias = trim($parts[0]);
                        $param = isset($parts[1]) ? trim($parts[1]) : null;

                        if (isset(self::$middlewareAliases[$alias])) {
                            $middlewareClass = self::$middlewareAliases[$alias];
                            if (class_exists($middlewareClass)) {
                                $instance = new $middlewareClass();
                                $allowed = $instance->handle($param);
                                if ($allowed === false) {
                                    return null;
                                }
                            }
                        }
                    }
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return self::executeAction($route['action'], array_values($params));
            }
        }

        // 404 Fallback
        http_response_code(404);
        if (file_exists(__DIR__ . '/../../resources/views/errors/404.php')) {
            echo view('errors.404');
        } else {
            echo "<!DOCTYPE html><html><head><title>404 Not Found - CamHub</title></head><body style='font-family:sans-serif;text-align:center;padding:50px;'><h1>404 | Halaman Tidak Ditemukan</h1><p>Halaman yang Anda tuju tidak ditemukan di platform CamHub.</p><a href='" . base_url() . "' style='color:#2563eb;'>Kembali ke Beranda</a></body></html>";
        }
        return null;
    }

    /**
     * Execute route handler callback or controller method
     */
    private static function executeAction(callable|array|string $action, array $params = []): mixed
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }

        if (is_array($action) && count($action) === 2) {
            [$class, $method] = $action;
            if (class_exists($class)) {
                $controller = new $class();
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $params);
                }
            }
        }

        http_response_code(500);
        die("Handler route tidak valid atau class/method tidak ditemukan.");
    }

    /**
     * Clear all routes (useful for testing)
     */
    public static function clearRoutes(): void
    {
        self::$routes = [];
    }

    /**
     * Get all registered routes
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }
}
