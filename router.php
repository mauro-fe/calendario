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
        // Remove query strings da URL
        $route = strtok($requestedPath, '?'); // Ignora parâmetros na URL
        $route = rtrim($route, '/');
        $route = $route === '' ? '/' : $route;

        error_log("Rota solicitada: " . $route); // Log para depuração

        foreach (self::$routes as $routeConfig) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', trim($routeConfig['path'], '/'));
            $pattern = "#^$pattern$#";

            if ($_SERVER['REQUEST_METHOD'] === $routeConfig['method'] && preg_match($pattern, trim($route, '/'), $matches)) {
                array_shift($matches);

                // Adiciona os parâmetros de query string como último argumento
                $matches[] = $_GET;

                if (is_callable($routeConfig['callback'])) {
                    call_user_func_array($routeConfig['callback'], $matches);
                    return;
                }

                if (is_string($routeConfig['callback'])) {
                    list($controller, $method) = explode('@', $routeConfig['callback']);
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
