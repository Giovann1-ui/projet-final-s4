<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Mobile Money</title>
    <link href="<?= base_url('asset/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .login-card { max-width: 480px; margin: 5rem auto; }
    </style>
</head>
<body>
<div class="container login-card">
    <h3 class="mb-4 text-center"><i class="bi bi-wallet2"></i> Mobile Money</h3>
    <p class="text-muted text-center">Entrez votre numéro de téléphone pour accéder à votre compte.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('client/login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <input
                type="text"
                name="num_tel"
                class="form-control form-control-lg"
                placeholder="Numéro de téléphone"
                value="<?= esc(old('num_tel')) ?>"
                required
            >
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-box-arrow-in-right"></i> Se connecter
        </button>
    </form>

    <hr class="my-4">

    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-secondary btn-lg w-100">
        <i class="bi bi-person-badge"></i> Espace opérateur
    </a>
</div>
</body>
</html>
