<?php
class Router
{
    private static $routes = [];

    // Adiciona uma rota
    public static function add($method, $path, $callback)
    {
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'callback' => $callback,
        ];
    }

    // Executa o roteador
    public static function run($requestedPath)
    {
        $requestedPath = rtrim($requestedPath, '/'); // Remove barra final, se houver
        $requestedPath = $requestedPath === '' ? '/' : $requestedPath;

        foreach (self::$routes as $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', trim($route['path'], '/'));
            $pattern = "#^$pattern$#";

            // Verifica o método HTTP e se a rota corresponde
            if ($_SERVER['REQUEST_METHOD'] === $route['method'] && preg_match($pattern, trim($requestedPath, '/'), $matches)) {
                array_shift($matches);

                if (is_callable($route['callback'])) {
                    call_user_func_array($route['callback'], $matches);
                    return;
                }

                if (is_string($route['callback'])) {
                    list($controller, $method) = explode('@', $route['callback']);
                    $controllerFile = __DIR__ . "/App/Controllers/" . $controller . ".php";

                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        $controllerInstance = new $controller();
                        call_user_func_array([$controllerInstance, $method], $matches);
                    } else {
                        http_response_code(404);
                        echo "Controlador '$controller' não encontrado!";
                    }
                    return;
                }
            }
        }

        // Página 404
        http_response_code(404);
        echo "Página não encontrada!";
    }

    // Middleware para verificar se o admin está logado
    public static function adminMiddleware()
    {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /admin/login');
            exit;
        }
    }
}
