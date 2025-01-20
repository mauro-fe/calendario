<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FullCalendar CSS -->

</head>

<body>
        <div id="calendar"></div>

    <!-- Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Adicionar Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Título</label>
                            <input type="text" class="form-control" id="eventTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventStart" class="form-label">Início</label>
                            <input type="datetime-local" class="form-control" id="eventStart" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventEnd" class="form-label">Término</label>
                            <input type="datetime-local" class="form-control" id="eventEnd" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveEvent">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Seu script -->

    <script src="fullcalendar/assets/js/jquery-3.7.1.min.js"></script>
    <script src="fullcalendar/dist/index.global.min.js"></script>
    <script src="fullcalendar/assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>