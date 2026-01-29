<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">🛠️ Admin Dashboard</h1>
        <span class="badge bg-danger p-2">Benvenuto, <?= esc(session()->get('nome')) ?></span>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-12">
            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between align-items-center">
                    <span>🏆 Livelli & Sconti Fedeltà</span>
                    <small>Gestisci le soglie punti</small>
                </div>
                <div class="card-body">
                    
                    <?php 
                        // Recupero regole usando WHERE per evitare errori di ID
                        $confModel = new \App\Models\ConfigurazioniModel();
                        $dataDb = $confModel->where('chiave', 'regole_fedelta')->first();
                        $regole = ($dataDb) ? json_decode($dataDb['valore'], true) : [];
                    ?>

                    <?php if (!empty($regole)): ?>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered text-center align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Soglia Punti</th>
                                        <th>Sconto %</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($regole as $index => $r): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">⭐ <?= $r['punti'] ?> Punti</td>
                                        <td class="fw-bold text-success">-<?= $r['sconto'] ?>%</td>
                                        <td>
                                            <a href="<?= base_url('admin/rimuoviRegolaFedelta/'.$index) ?>" 
                                               class="btn btn-danger btn-sm py-0 px-2" 
                                               title="Rimuovi"
                                               onclick="return confirm('Vuoi davvero rimuovere questo livello?');">
                                                &times;
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border text-center small text-muted p-2 mb-3">
                            <i class="bi bi-info-circle"></i> Nessun livello di fedeltà attivo.
                        </div>
                    <?php endif; ?>

                    <h6 class="fw-bold small text-uppercase text-muted border-top pt-2">Nuovo Livello</h6>
                    <form action="<?= base_url('admin/aggiungiRegolaFedelta') ?>" method="post" class="row g-2 align-items-end">
                        <div class="col-md-5 col-5">
                            <label class="small fw-bold">Punti</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">⭐</span>
                                <input type="number" name="punti" class="form-control" placeholder="es. 100" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-5 col-5">
                            <label class="small fw-bold">Sconto %</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">%</span>
                                <input type="number" name="sconto" class="form-control" placeholder="es. 20" min="1" max="100" required>
                            </div>
                        </div>
                        <div class="col-md-2 col-2">
                            <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
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
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,0.1) !important; }
</style>

<?= $this->endSection() ?>