<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliba Store</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url('logo.jpg') ?>">
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
        
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= session()->get('ruolo') == 'admin' ? base_url('admin/dashboard') : base_url('/') ?>">
            <?php if (session()->get('ruolo') == 'admin'): ?>
                <span>🛡️ ADMIN PANEL</span>
            <?php else: ?>
                <img src="<?= base_url('logo.jpg') ?>" alt="Logo" width="30" height="30" class="d-inline-block align-text-top rounded">
                <span>Poliba Store</span>
            <?php endif; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
    
            <?php if (session()->get('ruolo') == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/prodotti') ?>">Gestione Prodotti</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/inventario') ?>">Inventario</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/ordini') ?>">Ordini</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/statistiche') ?>">Vendite</a></li>

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
                
                <?php if (session()->get('ruolo') != 'admin'): ?>
                <li class="nav-item me-3 d-none d-lg-block">
                    <span class="badge bg-warning text-dark shadow-sm d-flex align-items-center gap-1" title="I tuoi Punti Fedeltà" style="font-size: 0.9rem;">
                        ⭐ <?= session()->get('punti_fedelta') ?? 0 ?> Punti
                    </span>
                </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= esc(session()->get('nome')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><span class="dropdown-item-text small text-muted text-uppercase"><?= session()->get('ruolo') ?></span></li>
                        
                        <li class="d-lg-none">
                            <span class="dropdown-item-text fw-bold text-warning">
                                ⭐ Punti: <?= session()->get('punti_fedelta') ?? 0 ?>
                            </span>
                        </li>
                        
                        <?php if (session()->get('ruolo') != 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('profilo') ?>"><i class="bi bi-card-list"></i> I miei Ordini</a></li>
                        <?php endif; ?>

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
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-dark text-white pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold text-uppercase text-warning mb-3">Poliba Store</h5>
                    <p class="small text-secondary">
                        Official Politecnico di Bari merch
                    </p>
                    <p class="small text-secondary">
                        Made for minds that build the future ✨
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold text-uppercase mb-3">📍 Dove Siamo</h5>
                    <p class="mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i> Via Edoardo Orabona, 4 BARI</p>
                    <p class="mb-1"><i class="bi bi-telephone-fill text-success me-2"></i> 080 111 1111</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold text-uppercase mb-3">🕒 Orari Apertura</h5>
                    <p class="small text-white-50">Lun-Ven: 09:00 - 16:30</p>
                    <div class="mt-3">
                        <small class="text-muted">&copy; <?= date('Y') ?> Tutti i diritti riservati.</small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>