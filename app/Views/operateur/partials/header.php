<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money — Espace opérateur</title>
    <link href="<?= base_url('asset/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .page-card { max-width: 960px; margin: 2rem auto; }
        .op-box {
            border-radius: .75rem;
            border: 1px solid #e2e5ea;
            padding: 1rem 1.25rem;
            height: 100%;
        }
        .op-box .libelle { font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; }
        .op-box .montant { font-size: 1.4rem; font-weight: 600; }
        .total-box { border-radius: .75rem; background: #212529; color: #fff; padding: 1.5rem; }
        .total-box .montant { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('operateur') ?>"><i class="bi bi-person-badge"></i> Mobile Money — Opérateur</a>
        <div class="navbar-nav ms-auto flex-row flex-wrap gap-3">
            <?php $active = $activePage ?? ''; ?>
            <a class="nav-link <?= $active === 'dashboard' ? 'fw-bold text-white' : 'text-white-50' ?>" href="<?= base_url('operateur') ?>">Tableau de bord</a>
            <a class="nav-link <?= $active === 'creer' ? 'fw-bold text-white' : 'text-white-50' ?>" href="<?= base_url('operateur/creer') ?>">Préfixes</a>
            <a class="nav-link <?= $active === 'gains' ? 'fw-bold text-white' : 'text-white-50' ?>" href="<?= base_url('operateur/situation-gains') ?>">Situation des gains</a>
            <a class="nav-link <?= $active === 'clients' ? 'fw-bold text-white' : 'text-white-50' ?>" href="<?= base_url('operateur/situation-clients') ?>">Situation des clients</a>
            <a class="nav-link text-white-50" href="<?= base_url('client/login') ?>">Espace client</a>
        </div>
    </div>
</nav>
<div class="container page-card">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
