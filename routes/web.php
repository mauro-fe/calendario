<?php
require_once 'router.php';

// Rotas para eventos
Router::add('GET', '/events', 'EventController@index'); // Listar eventos
Router::add('POST', '/events/add', 'EventController@store'); // Adicionar evento
Router::add('POST', '/events/update/{id}', 'EventController@update'); // Atualizar evento
Router::add('POST', '/events/delete/{id}', 'EventController@delete'); // Remover evento
