<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="position-relative p-5 mb-5 text-center text-white rounded-3 shadow" 
     style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center; height: 60vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="col-md-8 mx-auto">
        <h1 class="display-3 fw-bold">Nuova Collezione 2026</h1>
        <p class="fs-4 mb-4">Esprimi il tuo stile con capi unici. Qualità premium e design esclusivo.</p>
        <a href="#shop-section" class="btn btn-warning btn-lg px-5 fw-bold rounded-pill shadow">Scopri lo Shop</a>
    </div>
</div>

<div class="container mb-5">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="p-4 border rounded bg-white shadow-sm h-100">
                <h4 class="text-primary fw-bold">📍 Dove Siamo</h4>
                <p class="mb-0 text-muted">Corso Italia 10, Milano</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border rounded bg-white shadow-sm h-100">
                <h4 class="text-primary fw-bold">🕒 Orari Apertura</h4>
                <p class="mb-0 text-muted">Lun - Sab: 09:30 - 19:30<br>Domenica: Chiuso</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border rounded bg-white shadow-sm h-100">
                <h4 class="text-primary fw-bold">📞 Servizio Clienti</h4>
                <p class="mb-0 text-muted">supporto@mystore.it<br>+39 02 1234567</p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5" id="shop-section">
    <div class="text-center mb-5">
        <h2 class="fw-bold">I Nostri Prodotti</h2>
        <p class="text-muted">Esplora le ultime novità selezionate per te</p>
    </div>
    
    <div class="row">
        <?php if (!empty($prodotti) && is_array($prodotti)): ?>
            
            <?php foreach ($prodotti as $prodotto): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-effect overflow-hidden">
                        
                        <?php 
                            // Percorso immagine aggiornato per leggere dal file system
                            if (!empty($prodotto['immagine'])) {
                                $imgSrc = base_url('uploads/prodotti/' . $prodotto['immagine']);
                            } else {
                                $imgSrc = 'https://via.placeholder.com/400x300?text=No+Image';
                            }
                        ?>
                        
                        <a href="<?= base_url('prodotto/' . $prodotto['id']) ?>" class="overflow-hidden d-block">
                            <img src="<?= $imgSrc ?>" class="card-img-top transition-zoom" alt="<?= esc($prodotto['nome']) ?>" style="height: 280px; object-fit: cover;">
                        </a>

                        <div class="card-body d-flex flex-column p-4">
                            <div class="mb-3">
                                <h5 class="card-title fw-bold mb-1">
                                    <a href="<?= base_url('prodotto/' . $prodotto['id']) ?>" class="text-decoration-none text-dark stretched-link">
                                        <?= esc($prodotto['nome']) ?>
                                    </a>
                                </h5>
                                <span class="badge bg-light text-dark border"><?= esc($prodotto['vestibilita']) ?> Fit</span>
                            </div>

                            <h4 class="text-primary fw-bold mb-3">€ <?= number_format($prodotto['prezzo'], 2, ',', '.') ?></h4>

                            <?php 
                                // Calcolo disponibilità stock
                                $totalePezzi = 0;
                                $magazzino = $prodotto['magazzino'];
                                if (is_string($magazzino)) {
                                    $magazzino = json_decode($magazzino, true);
                                }
                                if (is_array($magazzino)) {
                                    foreach($magazzino as $taglia => $colori) {
                                        if (is_array($colori)) {
                                            foreach($colori as $qty) {
                                                $totalePezzi += (int)$qty;
                                            }
                                        }
                                    }
                                }
                            ?>

                            <div class="mt-auto position-relative" style="z-index: 2;"> 
                                <?php if ($totalePezzi > 0): ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-success fw-bold"><i class="bi bi-check-circle"></i> Disponibile</small>
                                        <small class="text-muted"><?= $totalePezzi ?> pezzi rimasti</small>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <a href="<?= base_url('prodotto/' . $prodotto['id']) ?>" class="btn btn-dark fw-bold py-2">
                                            Scegli Taglia e Colore
                                        </a>
                                    </div>

                                <?php else: ?>
                                    
                                    <div class="alert alert-danger py-2 text-center mb-0 fw-bold small">
                                        ❌ ESAURITO
                                    </div>

                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="py-5 bg-light rounded shadow-sm">
                    <i class="bi bi-inbox text-muted display-1"></i>
                    <h3 class="text-muted mt-3">Il negozio è vuoto al momento.</h3>
                    <p>Stiamo rifornendo gli scaffali. Torna a trovarci presto!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .transition-zoom {
        transition: transform 0.4s ease;
    }
    .hover-effect:hover .transition-zoom {
        transform: scale(1.1);
    }
    .hover-effect {
        transition: box-shadow 0.3s ease;
    }
    .hover-effect:hover {
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
    }
</style>

<?= $this->endSection() ?>