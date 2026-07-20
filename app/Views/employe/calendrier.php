<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Calendrier<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Calendrier<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('employe') ?>">Accueil</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> Mon Calendrier<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $statutLabels = [
        'en_attente' => 'En attente',
        'approuvee' => 'Approuvée',
        'refusee' => 'Refusée',
        'annulee' => 'Annulée',
    ];
    $statutColors = [
        'en_attente' => '#f08c00',
        'approuvee' => '#2f9e44',
        'refusee' => '#e03131',
        'annulee' => '#868e96',
    ];

    $events = [];
    foreach ($demandes as $d) {
        $statut = $d['statut'] ?? 'en_attente';
        $label = $statutLabels[$statut] ?? 'En attente';
        $color = $statutColors[$statut] ?? '#4c6ef5';
        $events[] = [
            'title' => ($d['libelle'] ?? 'Congé') . ' (' . $label . ')',
            'start' => $d['date_debut'],
            'end' => date('Y-m-d', strtotime($d['date_fin'] . ' +1 day')),
            'allDay' => true,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'textColor' => '#ffffff',
            'extendedProps' => [
                'statut' => $statut,
                'jours' => (int) $d['nb_jours'],
            ],
        ];
    }
?>

<div class="data-card" style="margin:0">
    <div class="data-card-head">
        <h3>Mon calendrier hebdomadaire</h3>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;font-size:.78rem;color:var(--muted)">
            <span><span style="display:inline-block;width:10px;height:10px;background:#2f9e44;border-radius:50%;margin-right:5px"></span>Approuvée</span>
            <span><span style="display:inline-block;width:10px;height:10px;background:#f08c00;border-radius:50%;margin-right:5px"></span>En attente</span>
            <span><span style="display:inline-block;width:10px;height:10px;background:#e03131;border-radius:50%;margin-right:5px"></span>Refusée</span>
            <span><span style="display:inline-block;width:10px;height:10px;background:#868e96;border-radius:50%;margin-right:5px"></span>Annulée</span>
        </div>
    </div>
    <div id="calendar" style="min-height:520px"></div>
</div>

<script src="<?= base_url('asset/js/index.global.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const events = <?= json_encode($events) ?>;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'fr',
            firstDay: 1,
            nowIndicator: true,
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,dayGridMonth,timeGridDay'
            },
            events: events,
            dateClick: function (info) {
                calendar.changeView('timeGridDay', info.dateStr);
            },
            eventClick: function (info) {
                const statut = info.event.extendedProps.statut || 'en_attente';
                const jours = info.event.extendedProps.jours || 0;
                info.jsEvent.preventDefault();
                alert(info.event.title + '\nDurée: ' + jours + ' jour(s)\nStatut: ' + statut.replace('_', ' '));
            }
        });

        calendar.render();
    });
</script>
<?= $this->endSection() ?>