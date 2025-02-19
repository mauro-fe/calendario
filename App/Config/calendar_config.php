<?php
// Define se a visualização é semanal
$isWeeklyView = isset($_GET['view']) && $_GET['view'] === 'week';

// Obtém a data atual e configura o fuso horário
$today = new DateTime();
$today->setTimezone(new DateTimeZone('America/Sao_Paulo')); // Define o fuso horário para Brasília

// Determina o primeiro e o último dia da semana
$firstDayOfWeek = clone $today;
$firstDayOfWeek->modify('last sunday'); // Obtém o primeiro dia da semana (domingo)

$lastDayOfWeek = clone $firstDayOfWeek;
$lastDayOfWeek->modify('+6 days'); // Obtém o último dia da semana (sábado)

// Garante que $eventList seja sempre um array
$eventList = isset($events) && $events instanceof \Illuminate\Database\Eloquent\Collection
    ? $events->toArray()
    : (is_array($events) ? $events : []);
