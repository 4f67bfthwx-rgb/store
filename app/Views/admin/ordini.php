<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    // ============================================================
    // PREPARAZIONE DATI MAGAZZINO PER JAVASCRIPT
    // ============================================================
    $db = \Config\Database::connect();
    $listaProdotti = $db->table('prodotti')->get()->getResultArray();

    // Array per JS
    $stockData = [];
    foreach ($listaProdotti as $prod) {
        $magazzino = json_decode($prod['magazzino'] ?? '{}', true);
        if (is_array($magazzino)) {
            $stockData[$prod['id']] = $magazzino; 
        }
    }
?>

<script>
    const databaseMagazzino = <?= json_encode($stockData); ?>;
</script>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">📦 Gestione Ordini</h2>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow: visible;"> 
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Cliente</th>
                            <th style="width: 35%;">Prodotti & Omaggi</th>
                            <th class="text-center">Stato</th>
                            <th>Totale</th>
                            <th>Data</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($ordini)): ?>
                            <?php foreach($ordini as $o): ?>
                            <tr>
                                <td class="fw-bold">#<?= $o['id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($o['nome_cliente']) ?></div>
                                    <div class="small text-muted"><?= esc($o['email']) ?></div>                                </td>
                                <td>
                                    <?php 
                                        $prodotti = json_decode($o['dettagli_prodotti'], true);
                                        if(is_array($prodotti) && !empty($prodotti)): 
                                            foreach($prodotti as $p): ?>
                                                <div class="mb-2 pb-1 border-bottom border-light">
                                                    <a href="<?= base_url('prodotto/' . $p['id']) ?>" target="_blank" class="fw-bold text-decoration-none">
                                                        <?= esc($p['nome']) ?>
                                                    </a>
                                                    <small class="text-secondary d-block">
                                                        <?= esc($p['taglia']) ?> | <?= esc($p['colore']) ?> | Qtà: <?= esc($p['quantita']) ?>
                                                    </small>
                                                </div>
                                            <?php endforeach; 
                                        endif; 
                                    ?>

                                    <?php 
                                        $omaggi = json_decode($o['omaggi'] ?? '', true);
                                        
                                        if (!is_array($omaggi) && !empty($o['omaggi'])): 
                                            echo '<div class="alert alert-warning p-1 small mb-0"><i class="bi bi-gift-fill"></i> Nota: '.esc($o['omaggi']).'</div>';
                                        
                                        elseif (is_array($omaggi) && !empty($omaggi)): 
                                    ?>
                                        <div class="mt-2 p-2 bg-success bg-opacity-10 border border-success rounded">
                                            <strong class="text-success small text-uppercase"><i class="bi bi-gift-fill"></i> Omaggi Inclusi:</strong>
                                            <?php foreach($omaggi as $gift): ?>
                                                <div class="small text-dark mt-1">
                                                    • <strong><?= esc($gift['nome']) ?></strong> 
                                                    (<?= esc($gift['taglia']) ?>, <?= esc($gift['colore']) ?>) x<?= esc($gift['quantita']) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $badgeClass = 'secondary'; 
                                        switch($o['stato']) {
                                            case 'In lavorazione': $badgeClass = 'warning text-dark'; break;
                                            case 'Spedito':        $badgeClass = 'info text-white'; break;
                                            case 'Consegnato':     $badgeClass = 'success'; break;
                                            case 'Annullato':      $badgeClass = 'danger'; break;
                                        }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= esc($o['stato']) ?></span>
                                </td>
                                <td class="fw-bold text-success">€ <?= number_format($o['totale'], 2, ',', '.') ?></td>
                                <td class="small"><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                                <td class="text-end pe-3">
                                    
                                    <div class="btn-group dropstart">
                                        <button type="button" class="btn btn-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            Gestisci
                                        </button>
                                        <ul class="dropdown-menu shadow">
                                            <li><h6 class="dropdown-header">Stato Ordine</h6></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/cambiaStato/' . $o['id'] . '/In lavorazione') ?>">🟡 In Lavorazione</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/cambiaStato/' . $o['id'] . '/Spedito') ?>">🚚 Spedito</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/cambiaStato/' . $o['id'] . '/Consegnato') ?>">✅ Consegnato</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <li>
                                                <button type="button" class="dropdown-item text-success fw-bold" 
                                                        onclick="apriModalOmaggio(<?= $o['id'] ?>)">
                                                    🎁 Aggiungi Omaggio
                                                </button>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="<?= base_url('admin/cambiaStato/' . $o['id'] . '/Annullato') ?>">❌ Annulla Ordine</a></li>
                                        </ul>
                                    </div>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x display-4 d-block mb-2"></i>
                                    Non ci sono ordini da visualizzare.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOmaggio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">🎁 Regala Prodotto da Magazzino</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/aggiungiOmaggio') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_ordine" id="modalIdOrdine">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Seleziona Prodotto</label>
                        <select name="id_prodotto" id="selectProdotto" class="form-select" required onchange="caricaTaglie()">
                            <option value="">-- Scegli dal Catalogo --</option>
                            <?php foreach($listaProdotti as $prod): 
                                // FILTRO PHP: Calcoliamo se il prodotto ha almeno 1 pezzo in totale
                                $totaleStock = 0;
                                $mag = json_decode($prod['magazzino'] ?? '{}', true);
                                if(is_array($mag)) {
                                    foreach($mag as $t => $colors) {
                                        foreach($colors as $c => $q) { $totaleStock += $q; }
                                    }
                                }
                                if($totaleStock > 0):
                            ?>
                                <option value="<?= $prod['id'] ?>"><?= esc($prod['nome']) ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">2. Taglia</label>
                            <select name="taglia" id="selectTaglia" class="form-select" required disabled onchange="caricaColori()">
                                <option value="">Prima scegli prodotto</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">3. Colore</label>
                            <select name="colore" id="selectColore" class="form-select" required disabled onchange="checkMaxQty()">
                                <option value="">Prima scegli taglia</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold">4. Quantità</label>
                            <div class="input-group">
                                <input type="number" name="quantita" id="inputQty" class="form-control" value="1" min="1" required>
                                <span class="input-group-text" id="maxQtyLabel">Max: -</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success">Conferma Omaggio</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function apriModalOmaggio(idOrdine) {
        document.getElementById('modalIdOrdine').value = idOrdine;
        var myModal = new bootstrap.Modal(document.getElementById('modalOmaggio'));
        myModal.show();
    }

    // --- LOGICA DINAMICA MENU A TENDINA ---
    
    function caricaTaglie() {
        const prodId = document.getElementById('selectProdotto').value;
        const selectTaglia = document.getElementById('selectTaglia');
        const selectColore = document.getElementById('selectColore');
        
        // Reset
        selectTaglia.innerHTML = '<option value="">-- Seleziona Taglia --</option>';
        selectColore.innerHTML = '<option value="">-- Prima la taglia --</option>';
        selectColore.disabled = true;
        document.getElementById('maxQtyLabel').innerText = "Max: -";

        if (!prodId || !databaseMagazzino[prodId]) {
            selectTaglia.disabled = true;
            return;
        }

        selectTaglia.disabled = false;
        const stockProdotto = databaseMagazzino[prodId]; 

        Object.keys(stockProdotto).forEach(taglia => {
            let hasStock = false;
            const colori = stockProdotto[taglia];
            for (const c in colori) {
                if (colori[c] > 0) {
                    hasStock = true;
                    break;
                }
            }
            if (hasStock) {
                let option = document.createElement("option");
                option.value = taglia;
                option.text = taglia;
                selectTaglia.add(option);
            }
        });
    }

    function caricaColori() {
        const prodId = document.getElementById('selectProdotto').value;
        const tagliaScelta = document.getElementById('selectTaglia').value;
        const selectColore = document.getElementById('selectColore');

        selectColore.innerHTML = '<option value="">-- Seleziona Colore --</option>';
        document.getElementById('maxQtyLabel').innerText = "Max: -";

        if (!tagliaScelta) {
            selectColore.disabled = true;
            return;
        }

        selectColore.disabled = false;
        const coloriDisponibili = databaseMagazzino[prodId][tagliaScelta]; 

        Object.keys(coloriDisponibili).forEach(colore => {
            let qty = coloriDisponibili[colore];
            if (qty > 0) {
                let option = document.createElement("option");
                option.value = colore;
                option.text = colore + " (Disp: " + qty + ")";
                option.setAttribute('data-max', qty); 
                selectColore.add(option);
            }
        });
    }

    function checkMaxQty() {
        const selectColore = document.getElementById('selectColore');
        const inputQty = document.getElementById('inputQty');
        const maxLabel = document.getElementById('maxQtyLabel');

        const selectedOption = selectColore.options[selectColore.selectedIndex];
        
        if (!selectedOption || !selectedOption.getAttribute('data-max')) {
            maxLabel.innerText = "Max: -";
            return;
        }

        const max = selectedOption.getAttribute('data-max');

        if (max) {
            inputQty.max = max;
            maxLabel.innerText = "Max: " + max;
            if(parseInt(inputQty.value) > parseInt(max)) {
                inputQty.value = max;
            }
        }
    }
</script>

<?= $this->endSection() ?>