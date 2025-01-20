<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Validator.php';

use App\Models\Event;
use App\Helpers\Validator;
use App\Helpers\View;

class EventController
{
    // Método para exibir os eventos
    public function index()
    {
        // Busca os eventos no banco de dados
        $events = Event::all();

        // Converte para o formato esperado pelo FullCalendar
        $response = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => date('Y-m-d\TH:i:s', strtotime($event->start)), // Converte para formato ISO 8601
                'end' => date('Y-m-d\TH:i:s', strtotime($event->end)),     // Converte para formato ISO 8601
                'description' => $event->description,
            ];
        });

        // Renderiza a view do calendário
        $view = new View('calendar/index');
        $view->setHeader('calendar/header');
        $view->setFooter('calendar/footer');
        $view->render();
    }

    // Método para criar um novo evento
    public function store()
    {
        try {
            $data = [
                'title' => $_POST['title'] ?? null,
                'start' => $_POST['start'] ?? null,
                'end' => $_POST['end'] ?? null,
                'description' => $_POST['description'] ?? null,

            ];

            if (empty($data['title']) || empty($data['start']) || empty($data['end'])) {
                echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
                return;
            }

            Event::create($data);

            echo json_encode(['success' => true, 'message' => 'Evento adicionado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    // Método para atualizar um evento existente
    public function update($id)
    {
        // Verifica se o administrador está logado
        $this->checkAdminSession();

        if (!empty($_POST)) {
            $validator = new Validator();

            $rules = [
                'title' => 'required|max_len,255',
                'start' => 'required|date',
                'end'   => 'required|date|after_or_equal,start',
                'description' => 'nullable|max_len,500',
            ];
            var_dump($rules);exit;

            // Valida os dados enviados
            $validated = $validator->validate($_POST, $rules);

            if (!$validated['valid']) { // Verifica se a validação falhou
                $this->sendJsonResponse(false, 'Erro na validação dos dados.', $validated['errors']);
                return;
            }

            // Busca o evento pelo ID e atualiza
            $event = Event::findOrFail($id);
            $event->update($validated);

            // Retorna sucesso como JSON
            $this->sendJsonResponse(true, 'Evento atualizado com sucesso!', $event);
        }
    }

    // Método para deletar um evento
    public function delete($id)
    {
        // Verifica se o administrador está logado
        $this->checkAdminSession();

        // Busca o evento pelo ID e remove
        $event = Event::findOrFail($id);
        $event->delete();

        // Retorna sucesso como JSON
        $this->sendJsonResponse(true, 'Evento removido com sucesso!');
    }

    // Método para verificar se o administrador está logado
    private function checkAdminSession()
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }

    // Método para enviar respostas JSON
    private function sendJsonResponse($success, $message, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
        exit;
    }
}
