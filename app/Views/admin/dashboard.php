<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">🛠️ Admin Dashboard</h1>
        <span class="badge bg-danger p-2">Benvenuto, <?= esc(session()->get('nome')) ?></span>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-5">
                    <div class="display-4 mb-3">👕</div>
                    <h3 class="card-title fw-bold">Prodotti</h3>
                    <p class="text-muted">Aggiungi, modifica o elimina i vestiti dal negozio.</p>
                    <a href="<?= base_url('admin/prodotti') ?>" class="btn btn-primary w-100 fw-bold shadow-sm">Gestisci Prodotti</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-5">
                    <div class="display-4 mb-3">📦</div>
                    <h3 class="card-title fw-bold">Ordini</h3>
                    <p class="text-muted">Controlla le prenotazioni e prepara le spedizioni.</p>
                    <a href="<?= base_url('admin/ordini') ?>" class="btn btn-warning w-100 fw-bold text-dark shadow-sm">Vedi Ordini</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-5">
                    <div class="display-4 mb-3">📋</div>
                    <h3 class="card-title fw-bold">Inventario</h3>
                    <p class="text-muted">Aggiornamento rapido delle scorte per taglia e colore.</p>
                    <a href="<?= base_url('admin/inventario') ?>" class="btn btn-success w-100 fw-bold shadow-sm">Gestione Scorte</a>
                </div>
            </div>
        </div>


    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-5">
                    <div class="display-4 mb-3">📊</div>
                    <h3 class="card-title fw-bold">Vendite</h3>
                    <p class="text-muted">Analizza incassi e scopri i prodotti più venduti.</p>
                    <a href="<?= base_url('admin/statistiche') ?>" class="btn btn-info w-100 fw-bold text-white shadow-sm">Vedi Statistiche</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-5">
                    <div class="display-4 mb-3">🛡️</div>
                    <h3 class="card-title fw-bold">Staff</h3>
                    <p class="text-muted">Crea nuovi amministratori per aiutarti.</p>
                    <a href="<?= base_url('admin/nuovo-admin') ?>" class="btn btn-danger w-100 fw-bold shadow-sm">Crea Nuovo Admin</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Effetto hover per le card per renderle interattive */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,0.1) !important;
    }
</style>

<?= $this->endSection() ?>