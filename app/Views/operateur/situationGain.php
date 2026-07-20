<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Total Gains Retrait: <?= $totalGainsRetrait ?? 0 ?></p>
    <p>Total Gains Transfert: <?= $totalGainsTransfert ?? 0 ?></p>

    <h2>Liste des Opérations Retrait</h2>
    <?php if (!empty($listeOperationsRetrait)) { ?>
        <table>
            <tr>
                <th>Id du client</th>
                <th>Montant brut</th>
                <th>Frais/Gain</th>
                <th>Total sortant</th>
            </tr>
            <?php foreach ($listeOperationsRetrait as $operation) { ?>
            <tr>
                <td><?= $operation['client_source'] ?? 'Inconnue' ?></td>
                <td><?= $operation['montant_brut'] ?? 'Inconnu' ?></td>
                <td><?= $operation['frais'] ?? 'Inconnus' ?></td>
                <td><?= $operation['montant_sortant'] ?? 'Inconnus' ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <h2>Liste des operation de transfert</h2>
    <?php if (!empty($listeOperationsTransfert)) { ?>
        <table>
            <tr>
                <th>Id du client source</th>
                <th>Id du client destination</th>
                <th>Montant brut</th>
                <th>Frais/Gain</th>
                <th>Montant sortant</th>
                <th>Montant entrant</th>
            </tr>
            <?php foreach ($listeOperationsTransfert as $operation) { ?>
            <tr>
                <td><?= $operation['client_source'] ?? 'Inconnue' ?></td>
                <td><?= $operation['client_dest'] ?? 'Inconnue' ?></td>
                <td><?= $operation['montant_brut'] ?? 'Inconnu' ?></td>
                <td><?= $operation['frais'] ?? 'Inconnus' ?></td>
                <td><?= $operation['montant_sortant'] ?? 'Inconnus' ?></td>
                <td><?= $operation['montant_entrant'] ?? 'Inconnus' ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>
</html>