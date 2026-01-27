<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Shop - Pannello di Controllo</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --admin-color: #dc3545;
            --user-color: #0d6efd;
        }
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar-brand { font-weight: 800; letter-spacing: 1px; }
        .main-content { flex: 1; }
        .nav-link { font-weight: 500; }
        .badge-cart { font-size: 0.7rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark <?= session()->get('ruolo') == 'admin' ? 'bg-danger' : 'bg-dark' ?> sticky-top shadow">
      <div class="container">
        <a class="navbar-brand" href="<?= session()->get('ruolo') == 'admin' ? base_url('admin/dashboard') : base_url('/') ?>">
            <?= session()->get('ruolo') == 'admin' ? '🛡️ ADMIN PANEL' : '🔥 MY SHOP' ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
    
    <?php if (session()->get('ruolo') == 'admin'): ?>
        <?php if (session()->get('ruolo') == 'admin'): ?>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/prodotti') ?>">Gestione Prodotti</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/inventario') ?>">Inventario</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/ordini') ?>">Ordini</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/statistiche') ?>">Vendite</a></li>
<?php endif; ?>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link <?= (url_is('/')) ? 'active' : '' ?>" href="<?= base_url('/') ?>">Catalogo Prodotti</a>
        </li>
    <?php endif; ?>

</ul>
          
          <ul class="navbar-nav ms-auto align-items-center">
            
            <?php if (session()->get('ruolo') != 'admin'): ?>
                <?php 
                    $carrello = session()->get('carrello') ?? [];
                    $contaOggetti = 0;
                    foreach($carrello as $item) { $contaOggetti += $item['quantita']; }
                ?>
                <li class="nav-item me-3">
                    <a class="btn btn-outline-light position-relative" href="<?= base_url('carrello') ?>">
                        <i class="bi bi-cart-fill"></i>
                        <?php if($contaOggetti > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-cart">
                                <?= $contaOggetti ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('isLoggedIn')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= esc(session()->get('nome')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><span class="dropdown-item-text small text-muted text-uppercase"><?= session()->get('ruolo') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Esci</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link active" href="<?= base_url('login') ?>">Accedi</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm ms-2" href="<?= base_url('register') ?>">Registrati</a>
                </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-3">
        <?php if (session()->getFlashdata('msg')): ?>
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                <?= session()->getFlashdata('msg') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-1 fw-bold">My Shop - E-Commerce Locale</p>
            <p class="mb-0 small text-muted">&copy; 2026 Tutti i diritti riservati. Solo ritiro in sede.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>