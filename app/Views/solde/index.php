<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter mon solde</title>
    <link href="<?= base_url('asset/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .solde-card { max-width: 720px; margin: 3rem auto; }
        .op-box {
            border-radius: .75rem;
            border: 1px solid #e2e5ea;
            padding: 1rem 1.25rem;
            height: 100%;
        }
        .op-box .libelle { font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; }
        .op-box .montant { font-size: 1.4rem; font-weight: 600; }
        .total-box { border-radius: .75rem; background: #0d6efd; color: #fff; padding: 1.5rem; }
        .total-box .montant { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
<div class="container solde-card">
    <h3 class="mb-4"><i class="bi bi-wallet2"></i> Consulter le solde d'un client</h3>

    <form method="get" action="<?= base_url('solde') ?>" class="row g-2 mb-4">
        <div class="col-8 col-md-9">
            <input
                type="text"
                name="num_tel"
                class="form-control form-control-lg"
                placeholder="Numéro de téléphone (ex: 0331234567)"
                value="<?= esc($num_tel) ?>"
                required
            >
        </div>
        <div class="col-4 col-md-3">
            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-search"></i> Rechercher
            </button>
        </div>
    </form>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= esc($erreur) ?></div>
    <?php endif; ?>

    <?php if ($client): ?>
        <div class="total-box mb-4 text-center">
            <div>Solde total — <?= esc($client['num_tel']) ?></div>
            <div class="montant"><?= number_format($soldeTotal, 0, ',', ' ') ?> Ar</div>
        </div>

        <h6 class="mb-3 text-muted">Détail par type d'opération</h6>
        <div class="row g-3">
            <?php foreach ($details as $ligne): ?>
                <div class="col-12 col-md-4">
                    <div class="op-box">
                        <div class="libelle"><?= esc($ligne['libelle']) ?></div>
                        <div class="montant <?= $ligne['solde'] < 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($ligne['solde'], 0, ',', ' ') ?> Ar
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-arrow-down-circle text-success"></i> Entrant : <?= number_format($ligne['entrant'], 0, ',', ' ') ?> Ar
                            <br>
                            <i class="bi bi-arrow-up-circle text-danger"></i> Sortant : <?= number_format($ligne['sortant'], 0, ',', ' ') ?> Ar
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (!$erreur && $num_tel === ''): ?>
        <p class="text-muted">Saisissez un numéro de téléphone pour afficher le solde correspondant.</p>
    <?php endif; ?>
</div>
</body>
</html>
