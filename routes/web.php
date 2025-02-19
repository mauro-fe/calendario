<?php
require_once 'router.php';

// Rotas para eventos
Router::add('GET', '/', 'EventController@index');
Router::add('GET', '/events', 'EventController@getEvents');
Router::add('POST', '/events/add', 'EventController@store'); // Adicionar evento
Router::add('POST', '/events/update/{id}', 'EventController@update'); // Atualizar evento
Router::add('POST', '/events/delete', 'EventController@delete');

