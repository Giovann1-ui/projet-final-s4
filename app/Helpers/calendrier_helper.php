<?php

function compter_jours_ouvrables(string $debut, string $fin): int
{
    $start = new \DateTime($debut);
    $end   = new \DateTime($fin);

    $total  = (int) $start->diff($end)->days + 1;
    $semaine = intdiv($total, 7);
    $weekends = $semaine * 2;

    $reste = $total % 7;
    $jourDebut = (int) $start->format('N');

    for ($i = 0; $i < $reste; $i++) {
        $jour = ($jourDebut + $i - 1) % 7 + 1;
        if ($jour >= 6) {
            $weekends++;
        }
    }

    return $total - $weekends;
}

function compter_jours_calendaires(string $debut, string $fin): int
{
    $start = new \DateTime($debut);
    $end   = new \DateTime($fin);

    return (int) $start->diff($end)->days + 1;
}
