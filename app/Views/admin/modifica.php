<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0 fw-bold">✏️ Modifica Prodotto</h4>
                </div>
                <div class="card-body p-4">
                    
                    <form action="<?= base_url('admin/aggiorna/' . $prodotto['id']) ?>" method="post" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Prodotto</label>
                            <input type="text" name="nome" class="form-control" value="<?= esc($prodotto['nome']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrizione</label>
                            <textarea name="descrizione" class="form-control" rows="4" required><?= esc($prodotto['descrizione']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Prezzo (€)</label>
                                <input type="number" step="0.01" name="prezzo" class="form-control" value="<?= esc($prodotto['prezzo']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Vestibilità</label>
                                <select name="vestibilita" class="form-select">
                                    <option value="Standard" <?= $prodotto['vestibilita'] == 'Standard' ? 'selected' : '' ?>>Standard</option>
                                    <option value="Slim" <?= $prodotto['vestibilita'] == 'Slim' ? 'selected' : '' ?>>Slim Fit</option>
                                    <option value="Oversize" <?= $prodotto['vestibilita'] == 'Oversize' ? 'selected' : '' ?>>Oversize</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label class="form-label fw-bold">📦 Magazzino (Quantità per Taglia/Colore)</label>
                            <div class="p-3 bg-light border rounded">
                                <div id="container-varianti"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="aggiungiVariante()">+ Aggiungi Variante</button>
                            </div>
                            <textarea name="magazzino" id="inputMagazzino" class="d-none"></textarea>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Foto Principale</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if($prodotto['immagine']): ?>
                                    <img src="data:<?= $prodotto['immagine_type'] ?>;base64,<?= base64_encode($prodotto['immagine']) ?>" width="80" class="rounded border">
                                <?php endif; ?>
                                <input type="file" name="immagine" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Galleria Foto Aggiuntive</label>
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted">Carica nuove foto (puoi selezionarne più di una)</label>
                                <input type="file" class="form-control" name="galleria[]" multiple accept="image/*">
                            </div>

                            <div class="row g-2">
                                <?php if(!empty($galleria)): ?>
                                    <?php foreach($galleria as $foto): ?>
                                        <div class="col-4 col-md-2 position-relative">
                                            <img src="data:<?= $foto['foto_type'] ?>;base64,<?= base64_encode($foto['foto']) ?>" class="img-fluid rounded border">
                                            
                                            <a href="<?= base_url('admin/elimina_foto/'.$foto['id']) ?>" 
                                               class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                               style="padding: 0px 5px; font-size: 12px;" 
                                               onclick="return confirm('Eliminare questa foto?')">×</a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning fw-bold text-dark">Aggiorna Prodotto</button>
                            <a href="<?= base_url('admin/prodotti') ?>" class="btn btn-outline-secondary">Annulla</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FIX ERRORE ARRAY: Controlliamo se è array o stringa prima di stamparlo
    let magazzinoData = <?= 
        is_array($prodotto['magazzino']) 
        ? json_encode($prodotto['magazzino']) 
        : ($prodotto['magazzino'] ?: '{}') 
    ?>;
    
    // Se è arrivato come stringa, facciamo il parse
    if (typeof magazzinoData === 'string') {
        try { magazzinoData = JSON.parse(magazzinoData); } catch(e) { magazzinoData = {}; }
    }

    const container = document.getElementById('container-varianti');
    const inputFinale = document.getElementById('inputMagazzino');

    function disegnaRiga(taglia = '', colore = '', quantita = 0) {
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 align-items-center riga-variante';
        div.innerHTML = `
            <div class="col-3">
                <select class="form-select form-select-sm inp-taglia" onchange="aggiornaJSON()">
                    <option value="">Taglia</option>
                    ${['XS','S','M','L','XL','XXL'].map(t => `<option value="${t}" ${t===taglia?'selected':''}>${t}</option>`).join('')}
                </select>
            </div>
            <div class="col-3">
                <input type="text" class="form-control form-control-sm inp-colore" placeholder="Colore" value="${colore}" oninput="aggiornaJSON()">
            </div>
            <div class="col-3">
                <input type="number" class="form-control form-control-sm inp-qty" placeholder="Qta" value="${quantita}" min="0" oninput="aggiornaJSON()">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="rimuoviRiga(this)">×</button>
            </div>
        `;
        container.appendChild(div);
    }

    function aggiungiVariante() {
        disegnaRiga();
    }

    function rimuoviRiga(btn) {
        btn.closest('.riga-variante').remove();
        aggiornaJSON();
    }

    function aggiornaJSON() {
        let obj = {};
        document.querySelectorAll('.riga-variante').forEach(riga => {
            let t = riga.querySelector('.inp-taglia').value;
            let c = riga.querySelector('.inp-colore').value;
            let q = parseInt(riga.querySelector('.inp-qty').value) || 0;

            if(t && c) {
                if(!obj[t]) obj[t] = {};
                obj[t][c] = q;
            }
        });
        inputFinale.value = JSON.stringify(obj);
    }

    // --- AVVIO: POPOLIAMO LE RIGHE ---
    let haDati = false;
    if (magazzinoData) {
        for (const [taglia, colori] of Object.entries(magazzinoData)) {
            if(typeof colori === 'object') {
                for (const [colore, qty] of Object.entries(colori)) {
                    disegnaRiga(taglia, colore, qty);
                    haDati = true;
                }
            }
        }
    }

    if (!haDati) disegnaRiga();
    aggiornaJSON();

</script>

<?= $this->endSection() ?>