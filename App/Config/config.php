<?php
// Configurações do banco de dados
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "agenda_db"); // Nome do banco de dados

// Configuração da base URL
define("BASE_URL", "http://localhost/calendario/");

// // Iniciar sessões
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// Incluir o autoload do Composer
require_once __DIR__ . '/../../vendor/autoload.php';


use Illuminate\Database\Capsule\Manager as Capsule;

// Configuração do banco de dados com Eloquent
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => DB_HOST,
    'database'  => DB_NAME,
    'username'  => DB_USER,
    'password'  => DB_PASSWORD,
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '', // Prefixo de tabela (caso tenha)
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
