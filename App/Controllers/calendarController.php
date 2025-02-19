<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Models\Event;

class CalendarController
{
    public function getCalendarData($year = null, $month = null)
    {
        // Define o ano e o mês atuais, se não forem fornecidos
        $year = $year ?? filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT) ?? date('Y');
        $month = $month ?? filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT) ?? date('m');


        // Ajusta o mês e o ano
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        // Calcula o mês e ano anterior
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        // Calcula o mês e ano seguinte
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        // Obtém os eventos do banco de dados
        $events = Event::whereYear('start', $year)
            ->whereMonth('start', $month)
            ->get();

        // Dados para o calendário
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
        $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));

        return [
            'year' => $year,
            'month' => $month,
            'prevYear' => $prevYear,
            'prevMonth' => $prevMonth,
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'events' => $events,
            'monthName' => $monthName,
            'daysInMonth' => $daysInMonth,
            'firstDayOfMonth' => $firstDayOfMonth,
        ];
    }

    public function index()
    {
        $data = $this->getCalendarData();

        // Debug opcional para verificar os dados passados para a view
        // var_dump($data);

        $view = new View('calendar/index', $data);
        $view->render();
    }
}
