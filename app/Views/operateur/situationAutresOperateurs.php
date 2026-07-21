<?php
/*
les variavles depuis le conntroller
return view('operateur/situationAutresOperateurs', $this->viewData([
        'listeAutresOperateurs' => $listeAutresOperateurs,
        'listeGainsAutres'      => $listeGainsAutres,
        'listeOperationsAutres' => $listeOperationsAutres,
    ]));
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?= view('operateur/partials/header', ['activePage' => 'situation_autres']) ?>

    <div class="container-fluid py-4">
        <h3 class="mb-4">
            <i class="bi bi-diagram-3-fill me-2"></i>Situation des Autres Opérateurs
        </h3>

        <?php if (empty($listeAutresOperateurs)): ?>
            <div class="alert alert-info text-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>Aucun autre opérateur trouvé.
            </div>
        <?php else: ?>
            <?php foreach ($listeAutresOperateurs as $opId => $operateur): ?>
                <?php
                $gains = $listeGainsAutres[$opId] ?? 0.0;
                $operations = $listeOperationsAutres[$opId] ?? [];
                $prefixes = implode(', ', $operateur['prefixes'] ?? []);
                ?>

                <div class="card shadow-sm mb-5">
                    <!-- En-tête de la carte de l'opérateur -->
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0 fw-bold"><?= esc($operateur['libelle']) ?></h4>
                            <small class="opacity-75">
                                Préfixes : <?= !empty($prefixes) ? esc($prefixes) : '<em>Aucun</em>' ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark fs-6">
                                Commission : <?= number_format((float) ($operateur['commission'] ?? 0), 2, ',', ' ') ?> %
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Résumé du gain total généré -->
                        <div class="row mb-4">
                            <div class="col-12 col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <span class="text-muted d-block small">Gain total généré sur cet opérateur</span>
                                    <span class="fs-4 fw-bold text-success">
                                        <?= number_format((float) $gains, 0, ',', ' ') ?> Ar
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des opérations -->
                        <h5 class="mb-3 text-secondary">
                            <i class="bi bi-arrow-left-right me-1"></i>Opérations associées
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Client source</th>
                                        <th>Client destination</th>
                                        <th class="text-end">Montant brut</th>
                                        <th class="text-end">Frais</th>
                                        <th class="text-end">Commission</th>
                                        <th class="text-end">Montant entrant</th>
                                        <th class="text-end">Montant sortant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($operations)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">
                                                Aucune opération enregistrée pour cet opérateur.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($operations as $operation): ?>
                                            <tr>
                                                <td>
                                                    <?= isset($operation['date']) ? date('d/m/Y H:i', strtotime($operation['date'])) : '—' ?>
                                                </td>
                                                <td><?= esc($operation['num_tel_source'] ?? '—') ?></td>
                                                <td><?= esc($operation['num_tel_dest'] ?? '—') ?></td>
                                                <td class="text-end fw-bold">
                                                    <?= number_format((float) ($operation['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar
                                                </td>
                                                <td class="text-end text-muted">
                                                    <?= number_format((float) ($operation['frais'] ?? 0), 0, ',', ' ') ?> Ar
                                                </td>
                                                <td class="text-end">
                                                    <?= number_format((float) ($operation['commission'] ?? 0), 0, ',', ' ') ?> %
                                                </td>
                                                <td class="text-end text-success">
                                                    <?= number_format((float) ($operation['montant_entrant'] ?? 0), 0, ',', ' ') ?> Ar
                                                </td>
                                                <td class="text-end text-danger">
                                                    <?= number_format((float) ($operation['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?= view('operateur/partials/footer') ?>
</body>

</html>