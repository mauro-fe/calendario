<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Validator.php';

use App\Models\Event;
use App\Helpers\View;

class EventController
{
    // Método para buscar eventos e renderizar o calendário
    public function getEvents()
    {
        try {
            $year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT) ?? date('Y');
            $month = filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT) ?? date('m');

            // Ajusta os valores do mês (entre 1 e 12)
            if ($month < 1) {
                $month = 12;
                $year--;
            } elseif ($month > 12) {
                $month = 1;
                $year++;
            }

            // Calcula o mês anterior e o próximo
            $prevMonth = $month - 1;
            $prevYear = $year;
            if ($prevMonth < 1) {
                $prevMonth = 12;
                $prevYear--;
            }

            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }

            $events = Event::whereYear('start', $year)
                ->whereMonth('start', $month)
                ->get();

            $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $firstDayOfMonth = date('w', strtotime("$year-$month-01"));

            $viewData = [
                'year' => $year,
                'month' => $month,
                'prevYear' => $prevYear,
                'prevMonth' => $prevMonth,
                'nextYear' => $nextYear,
                'nextMonth' => $nextMonth,
                'monthName' => $monthName,
                'daysInMonth' => $daysInMonth,
                'firstDayOfMonth' => $firstDayOfMonth,
                'events' => $events,
            ];

            $view = new View('calendar/index', $viewData);
            $view->render();
        } catch (Exception $e) {
            error_log("Erro ao carregar eventos: " . $e->getMessage());
            http_response_code(500);
            echo "<h1>Erro ao carregar eventos</h1><p>Tente novamente mais tarde.</p>";
        }
    }




    // Página inicial do calendário
    public function index()
    {
        $this->getEvents();
    }

    // Adicionar evento (Formulário + Processamento)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars($_POST['title']);
            $start = $_POST['start_time']; // Data e hora de início
            $end = $_POST['end_time']; // Data e hora de término
            $description = htmlspecialchars($_POST['description']);

            // Validação: impedir datas passadas
            $currentDateTime = date('Y-m-d H:i:s'); // Data e hora atual
            if ($start < $currentDateTime || $end < $currentDateTime) {
                $_SESSION['error'] = 'Você não pode adicionar eventos no passado!';
                header('Location: /calendario');
                exit;
            }

            // Validação adicional: título, horário de início e término não podem ser vazios
            if (empty($title) || empty($start) || empty($end)) {
                $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos!';
                header('Location: /calendario');
                exit;
            }

            // Salva o evento
            Event::create([
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'description' => $description,
            ]);

            $_SESSION['success'] = 'Evento adicionado com sucesso!';
            header('Location: /calendario');
            exit;
        }
    }



    // Editar evento (Formulário + Processamento)
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars($_POST['title']);
            $start = $_POST['start'];
            $end = $_POST['end'];
            $description = htmlspecialchars($_POST['description']);

            // Valida os dados do formulário
            if (empty($title) || empty($start) || empty($end)) {
                $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos!';
                header('Location: /calendario');
                exit;
            }

            if (strtotime($start) > strtotime($end)) {
                $_SESSION['error'] = 'A data de início não pode ser maior que a data de término!';
                header('Location: /calendario');
                exit;
            }

            // Atualiza o evento
            $event = Event::findOrFail($id);
            $event->update([
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'description' => $description,
            ]);

            $_SESSION['success'] = 'Evento atualizado com sucesso!';
            header('Location: /calendario');
            exit;
        }
    }


    // Excluir evento
    public function delete()
    {
        try {
            // Obtém os dados enviados via POST
            $eventId = $_POST['id'] ?? null;

            if (empty($eventId) || !is_numeric($eventId)) {
                header('Location: /calendario?error=ID do evento inválido.');
                exit;
            }

            // Verifica se o evento existe e exclui
            $event = Event::findOrFail($eventId);
            $event->delete();

            $_SESSION['success'] = 'Evento excluído com sucesso!';
            header('Location: /calendario');
            exit;
        } catch (Exception $e) {
            error_log("Erro ao excluir evento: " . $e->getMessage());
            header('Location: /calendario?error=Erro ao excluir evento.');
            exit;
        }
    }
}
