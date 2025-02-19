<?php
require_once __DIR__ . '/../../App/config/calendar_config.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Dinâmico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .calendar {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .calendar-table {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            /* 7 colunas para os dias da semana */
            grid-template-rows: repeat(6, 1fr);
            /* 6 linhas para as semanas do mês */
            gap: 1px;
            /* Pequeno espaçamento entre as células */
            background-color: #ddd;
            /* Para visualizar melhor a estrutura */
        }

        .calendar-table th {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .calendar-table td {
            background-color: white;
            padding: 5px;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
           
            height: 100px;
            /* Define altura fixa */
            overflow: hidden;
        }

        .event-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-height: 60px;
            overflow-y: auto;
        }

        /* Cada evento dentro do grid */
        .event-title {
            font-size: 0.75rem;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            padding: 1px 4px;
            margin: 2px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            text-align: center;
            max-width: 90%;
        }

        /* Mantém o botão "+X mais" alinhado corretamente */
        .btn-see-more {
            font-size: 0.7rem;
            color: #007bff;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            text-align: center;
            display: block;
            margin-top: 3px;
        }

        .btn-see-more:hover {
            text-decoration: underline;
        }

        /* 🔹 Mantém todas as células com altura fixa mesmo quando têm eventos */
        .calendar-table td {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-table td strong {
            margin-bottom: 4px;
        }

        .week-view {
            display: <?= $isWeeklyView ? 'table' : 'none' ?>;
            width: 100%;
            border-collapse: collapse;
        }

        .month-view {
            display: <?= !$isWeeklyView ? 'table' : 'none' ?>;
        }

        .week-view th,
        .week-view td {
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: top;
            height: 50px;
            position: relative;
        }

        .hour-column {
            width: 80px;
            text-align: right;
            font-weight: bold;
            background-color: #f8f9fa;
        }   

        .event-block {
            position: absolute;
            left: 5px;
            right: 5px;
            background-color: #007bff;
            color: white;
            padding: 5px;
            border-radius: 5px;
            font-size: 0.8rem;
            text-align: center;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="calendar">
            <div class="calendar-header">
                <a href="/calendario/events?year=<?= $prevYear ?>&month=<?= $prevMonth ?>&view=<?= $isWeeklyView ? 'week' : 'month' ?>" class="btn btn-custom">&lt; Mês Anterior</a>
                <h4><?= $monthName . " " . $year; ?></h4>
                <a href="/calendario/events?year=<?= $nextYear ?>&month=<?= $nextMonth ?>&view=<?= $isWeeklyView ? 'week' : 'month' ?>" class="btn btn-custom">Próximo Mês &gt;</a>
            </div>

            <!-- Dropdown para alternar entre mês e semana -->
            <div class="text-center mt-2">
                <label for="viewSelector" class="me-2">Visualizar:</label>
                <select id="viewSelector" class="form-select d-inline-block w-auto">
                    <option value="month" <?= !$isWeeklyView ? 'selected' : ''; ?>>Mês Completo</option>
                    <option value="week" <?= $isWeeklyView ? 'selected' : ''; ?>>Semana Atual</option>
                </select>
            </div>

            <!-- Modo Mensal -->
            <table class="table month-view">
                <thead>
                    <tr>
                        <th>Dom</th>
                        <th>Seg</th>
                        <th>Ter</th>
                        <th>Qua</th>
                        <th>Qui</th>
                        <th>Sex</th>
                        <th>Sáb</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$isWeeklyView): ?>
                        <?php $date = 1; ?>
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <tr>
                                <?php for ($j = 0; $j < 7; $j++): ?>
                                    <?php if ($i === 0 && $j < $firstDayOfMonth): ?>
                                        <td></td>
                                    <?php elseif ($date > $daysInMonth): ?>
                                        <?php break; ?>
                                    <?php else: ?>
                                        <td>
                                            <strong><?= $date; ?></strong>
                                            <?php
                                            $formattedDate = sprintf("%s-%02d-%02d", $year, $month, $date);

                                            // Filtra eventos para o dia atual
                                            $dayEvents = array_filter($eventList, function ($event) use ($formattedDate) {
                                                return isset($event['start']) && date('Y-m-d', strtotime($event['start'])) === $formattedDate;
                                            });
                                            ?>
                                            <div class="event-container">
                                                <?php foreach (array_slice($dayEvents, 0, 2) as $event): ?>
                                                    <div class="event-title"><?= htmlspecialchars($event['title']); ?></div>
                                                <?php endforeach; ?>
                                                <?php if (count($dayEvents) > 2): ?>
                                                    <button class="btn-see-more" data-bs-toggle="modal" data-bs-target="#dayModal<?= $date; ?>">
                                                        +<?= count($dayEvents) - 3; ?> mais
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <?php $date++; ?>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </tr>
                        <?php endfor; ?>
                    <?php endif; ?>

                </tbody>
            </table>

            <!-- Modo Semanal -->
            <table class="table week-view" id="weekTable">
                <thead>
                    <tr>
                        <th class="hour-column"></th>
                        <th>Dom</th>
                        <th>Seg</th>
                        <th>Ter</th>
                        <th>Qua</th>
                        <th>Qui</th>
                        <th>Sex</th>
                        <th>Sáb</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($hour = 0; $hour < 24; $hour++): ?>
                        <tr>
                            <td class="hour-column"><?= str_pad($hour, 2, "0", STR_PAD_LEFT) ?>:00</td>
                            <?php for ($day = 0; $day < 7; $day++): ?>
                                <td>
                                    <?php
                                    $currentDate = clone $firstDayOfWeek;
                                    $currentDate->modify("+{$day} days");
                                    $formattedDate = $currentDate->format('Y-m-d');

                                    foreach ($events as $event) {
                                        if (date('Y-m-d', strtotime($event['start'])) === $formattedDate && date('H', strtotime($event['start'])) == $hour) {
                                            echo '<div class="event-block">' . htmlspecialchars($event['title']) . '</div>';
                                        }
                                    }
                                    ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const viewSelector = document.getElementById("viewSelector");

            viewSelector.addEventListener("change", function() {
                const selectedView = viewSelector.value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('view', selectedView);
                window.location.href = currentUrl.toString();
            });
        });
    </script>

</body>

</html>