<?php
// Carrega o roteador
require_once 'router.php';
require_once 'routes/web.php';

// Define a URL base (ajuste conforme necessário)
$baseURL = $_SERVER['REQUEST_URI'];

if (strpos($baseURL, "/calendario/") !== false) {
    $baseURL = rtrim(str_replace("/calendario/", '', $baseURL), '/');
}

// Remove parâmetros de query da URL
$route = strtok($baseURL, '?');

// Executa o roteador
Router::run($route);
?>

