<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?: 'Gestion des Congés' ?></title>
    <link href="<?= base_url('asset/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/js/index.global.min.js') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/js/chart.umd.min.js') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php if ($employe): ?>
<div class="app-wrap">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo-icon"><i class="fas fa-briefcase"></i></div>
            <div class="sidebar-brand-name">
                TechMada RH ETU004055 & ETU4235
                <span><?= $role === 'admin' ? 'Administration' : ($role === 'rh' ? 'Espace RH' : 'Espace employé') ?></span>
            </div>
        </div>

        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
            <?php if ($role === 'admin'): ?>
                <li><a href="<?= base_url('admin') ?>" class="<?= current_url() === base_url('admin') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="<?= base_url('admin/demandes') ?>" class="<?= strpos(current_url(), '/admin/demandes') !== false ? 'active' : '' ?>"><i class="fas fa-inbox"></i> Toutes les demandes</a></li>
                <li><a href="<?= base_url('admin/employes') ?>" class="<?= strpos(current_url(), '/admin/employes') !== false ? 'active' : '' ?>"><i class="fas fa-users"></i> Employés</a></li>
                <li><a href="<?= base_url('admin/departements') ?>" class="<?= strpos(current_url(), '/admin/departements') !== false ? 'active' : '' ?>"><i class="fas fa-building"></i> Départements</a></li>
                <li><a href="<?= base_url('admin/types-conge') ?>" class="<?= strpos(current_url(), '/admin/types-conge') !== false ? 'active' : '' ?>"><i class="fas fa-tags"></i> Types de congé</a></li>
                <li><a href="<?= base_url('admin/soldes') ?>" class="<?= strpos(current_url(), '/admin/soldes') !== false ? 'active' : '' ?>"><i class="fas fa-coins"></i> Soldes annuels</a></li>
                <li><a href="<?= base_url('admin/historique') ?>" class="<?= strpos(current_url(), '/admin/historique') !== false ? 'active' : '' ?>"><i class="fas fa-history"></i> Historique</a></li>

            <?php elseif ($role === 'rh'): ?>
                <li><a href="<?= base_url('rh') ?>" class="<?= current_url() === base_url('rh') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="<?= base_url('rh/demandes') ?>" class="<?= strpos(current_url(), '/rh/demandes') !== false ? 'active' : '' ?>"><i class="fas fa-inbox"></i> Demandes à traiter</a></li>
                <li><a href="<?= base_url('rh/soldes') ?>" class="<?= strpos(current_url(), '/rh/soldes') !== false ? 'active' : '' ?>"><i class="fas fa-coins"></i> Soldes employés</a></li>

            <?php elseif ($role === 'employe'): ?>
                <li><a href="<?= base_url('employe') ?>" class="<?= current_url() === base_url('employe') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="<?= base_url('employe/demande') ?>" class="<?= strpos(current_url(), '/employe/demande') !== false ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Nouvelle demande</a></li>
                <li><a href="<?= base_url('employe/mes-demandes') ?>" class="<?= strpos(current_url(), '/employe/mes-demandes') !== false ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Mes demandes</a></li>
                <li><a href="<?= base_url('employe/profil') ?>" class="<?= strpos(current_url(), '/employe/profil') !== false ? 'active' : '' ?>"><i class="fas fa-user"></i> Mon profil</a></li>
                <li><a href="<?= base_url('employe/calendrier') ?>" class="<?= strpos(current_url(), '/employe/calendrier') !== false ? 'active' : '' ?>"><i class="fas fa-calendar"></i> Calendrier</a></li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-user">
            <a href="<?= base_url('logout') ?>" style="display:block;margin-bottom:4px;color:rgba(255,255,255,.25);font-size:.78rem;padding:0 11px;text-decoration:none">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
            <div class="s-user-row">
                <div class="avatar av-green">
                    <?= strtoupper(substr($employe['prenom'] ?? '', 0, 1) . substr($employe['nom'] ?? '', 0, 1)) ?>
                </div>
                <div>
                    <div class="user-name"><?= esc($employe['prenom'] . ' ' . $employe['nom']) ?></div>
                    <div class="user-role"><?= $role === 'admin' ? 'Admin système' : ($role === 'rh' ? 'Responsable RH' : 'Employé') ?></div>
                </div>
            </div>
        </div>
    </nav>

    <div class="main">
        <header class="topbar">
            <div>
                <div class="topbar-title"><?= $this->renderSection('page_title') ?: 'Tableau de bord' ?></div>
                <div class="topbar-breadcrumb"><?= $this->renderSection('breadcrumb') ?: 'Accueil' ?></div>
            </div>
            <div class="topbar-actions">
                <?= $this->renderSection('page_actions') ?>
            </div>
        </header>

        <div class="content">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="flash flash-success">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="flash flash-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>

        <div class="footer-app">
            <i class="far fa-copyright"></i> <?= date('Y') ?> <span>TechMada RH <b>ETU004055 & ETU4235</b></span> &mdash; Projet CodeIgniter 4
        </div>
    </div>
</div>

<?php else: ?>
<!-- Auth pages: no sidebar -->
<div class="auth-page geo-bg">
    <?php if (session()->getFlashdata('error')): ?>
    <div style="position:fixed;top:1rem;left:50%;transform:translateX(-50%);z-index:1000;width:90%;max-width:400px" class="flash flash-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>
    <?= $this->renderSection('content') ?>
</div>
<?php endif; ?>
    <script src="<?= base_url('asset/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
