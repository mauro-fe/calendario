// const BASE_URL = "<?= BASE_URL; ?>";
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'events',
        editable: true,
        selectable: true,

        // Callback para seleção de intervalo
        select: function (info) {
            // Preencher os campos de data automaticamente
            document.getElementById('eventStart').value = info.startStr;
            document.getElementById('eventEnd').value = info.endStr;

            // Abrir a modal
            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            eventModal.show();

            // Lidar com o botão de salvar
            document.getElementById('saveEvent').onclick = function () {
                var title = document.getElementById('eventTitle').value;
                var start = document.getElementById('eventStart').value;
                var end = document.getElementById('eventEnd').value;
                var description = document.getElementById('description').value;

                console.log({ title, start, end, description }); // Verifique os dados aqui

                if (!title || !start || !end) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return;
                }

                $.post('events/add', {
                    title: title,
                    start: start,
                    end: end,
                    description: description
                }, function (response) {
                    if (response.success) {
                        alert(response.message);
                        calendar.refetchEvents();
                        eventModal.hide();
                    } else {
                        alert('Erro ao adicionar evento.');
                    }
                }).fail(function (error) {
                    console.error('Erro na comunicação:', error);
                    alert('Erro ao comunicar com o servidor.');
                });

            };

        },

        eventClick: function (info) {
            alert(
                'Título: ' + info.event.title +
                '\nInício: ' + info.event.start.toISOString() +
                '\nFim: ' + (info.event.end ? info.event.end.toISOString() : 'Não definido') +
                '\nDescrição: ' + (info.event.extendedProps.description || 'Sem descrição')
            );
        }
    });

    calendar.render();
});
