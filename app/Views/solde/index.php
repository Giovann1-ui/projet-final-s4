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
            <div class="montant"><?= number_format($solde['solde'], 0, ',', ' ') ?> Ar</div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="op-box">
                    <div class="libelle">Reçu (client_dest)</div>
                    <div class="montant text-success">
                        <?= number_format($solde['entrant'], 0, ',', ' ') ?> Ar
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="op-box">
                    <div class="libelle">Envoyé (client_source)</div>
                    <div class="montant text-danger">
                        <?= number_format($solde['sortant'], 0, ',', ' ') ?> Ar
                    </div>
                </div>
            </div>
        </div>
    <?php elseif (!$erreur && $num_tel === ''): ?>
        <p class="text-muted">Saisissez un numéro de téléphone pour afficher le solde correspondant.</p>
    <?php endif; ?>
</div>
</body>
</html>
