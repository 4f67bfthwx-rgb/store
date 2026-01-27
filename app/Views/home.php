<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="position-relative p-5 mb-5 text-center text-white rounded-3 shadow" 
     style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center; height: 60vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="col-md-8 mx-auto">
        <h1 class="display-3 fw-bold">Nuova Collezione 2026</h1>
        <p class="fs-4 mb-4">Esprimi il tuo stile con capi unici. Qualità premium e design esclusivo.</p>
        <a href="#shop-section" class="btn btn-warning btn-lg px-5 fw-bold rounded-pill">Scopri lo Shop</a>
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
                            // Percorso immagine dal file system
                            $imgSrc = !empty($prodotto['immagine']) 
                                ? base_url('uploads/prodotti/' . $prodotto['immagine']) 
                                : 'https://via.placeholder.com/400x300?text=No+Image';
                        ?>
                        
                        <a href="<?= base_url('prodotto/' . $prodotto['id']) ?>" class="overflow-hidden d-block">
                            <img src="<?= $imgSrc ?>" class="card-img-top transition-zoom" alt="<?= esc($prodotto['nome']) ?>" style="height: 320px; object-fit: cover;">
                        </a>

                        <div class="card-body d-flex flex-column p-4 text-center">
                            <div class="mb-2">
                                <h4 class="card-title fw-bold mb-1">
                                    <a href="<?= base_url('prodotto/' . $prodotto['id']) ?>" class="text-decoration-none text-dark">
                                        <?= esc($prodotto['nome']) ?>
                                    </a>
                                </h4>
                                <span class="badge bg-light text-dark border small text-uppercase"><?= esc($prodotto['vestibilita']) ?> Fit</span>
                            </div>

                            <h3 class="text-primary fw-bold mb-3">€ <?= number_format($prodotto['prezzo'], 2, ',', '.') ?></h3>

                            <?php 
                                // Estrazione Logica Varianti e Stock
                                $magazzino = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];
                                $totalePezzi = 0;
                                $taglieDisponibili = [];
                                $coloriDisponibili = [];

                                if (is_array($magazzino)) {
                                    foreach($magazzino as $taglia => $colori) {
                                        if (is_array($colori)) {
                                            foreach($colori as $colore => $qty) {
                                                if ($qty > 0) {
                                                    $totalePezzi += (int)$qty;
                                                    $taglieDisponibili[] = $taglia;
                                                    $coloriDisponibili[] = $colore;
                                                }
                                            }
                                        }
                                    }
                                }
                                $taglieUnique = array_unique($taglieDisponibili);
                                $coloriUnique = array_unique($coloriDisponibili);
                            ?>

                            <div class="mb-3 small">
                                <div class="text-muted mb-1">Taglie: <strong><?= !empty($taglieUnique) ? implode(', ', $taglieUnique) : '-' ?></strong></div>
                                <div class="text-muted">Colori: <strong><?= !empty($coloriUnique) ? implode(', ', $coloriUnique) : '-' ?></strong></div>
                            </div>

                            <div class="mt-auto pt-3 border-top"> 
                                <div class="mb-3">
                                    <?php if ($totalePezzi == 0): ?>
                                        <span class="badge bg-danger p-2 w-100">❌ ESAURITO</span>
                                    <?php elseif ($totalePezzi <= 5): ?>
                                        <span class="badge bg-warning text-dark p-2 w-100">⚠️ IN ESAURIMENTO</span>
                                    <?php endif; ?>
                                    </div>
                                
                                <div class="d-grid">
                                    <a href="<?= base_url('prodotto/' . $prodotto['id']) ?>" class="btn btn-dark fw-bold py-2 shadow-sm">
                                        Vedi Dettagli
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h3 class="text-muted">Nessun prodotto disponibile al momento.</h3>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .transition-zoom { transition: transform 0.4s ease; }
    .hover-effect:hover .transition-zoom { transform: scale(1.1); }
    .hover-effect { transition: all 0.3s ease; }
    .hover-effect:hover { box-shadow: 0 1rem 3rem rgba(0,0,0,0.15) !important; transform: translateY(-5px); }
</style>

<?= $this->endSection() ?>