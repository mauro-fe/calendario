<?php
// Carrega o roteador e as rotas
require_once 'router.php';
require_once 'routes/web.php';


// Obtém a URL base da requisição
$baseURL = $_SERVER['REQUEST_URI'];

// Ajusta o caminho base do projeto corretamente
$projectBase = "/calendario/"; // Defina conforme o seu projeto

if (strpos($baseURL, $projectBase) === 0) {
    $baseURL = substr($baseURL, strlen($projectBase));
}

// Remove parâmetros de query da URL para identificar a rota limpa
$route = strtok($baseURL, '?');

// Se a rota estiver vazia, defina um valor padrão (por exemplo, página inicial)
if (empty($route)) {
    $route = '/';
}

// Executa o roteador
Router::run($route);
