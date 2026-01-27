<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0 fw-bold">✨ Nuovo Prodotto</h4>
                </div>
                <div class="card-body p-4">
                    
                    <form action="<?= base_url('admin/salva') ?>" method="post" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Prodotto</label>
                            <input type="text" name="nome" class="form-control" placeholder="es. Polo in Cotone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrizione</label>
                            <textarea name="descrizione" class="form-control" rows="3" placeholder="Descrivi il prodotto..." required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Prezzo (€)</label>
                                <input type="number" step="0.01" name="prezzo" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Vestibilità</label>
                                <select name="vestibilita" class="form-select">
                                    <option value="Standard">Standard</option>
                                    <option value="Slim">Slim Fit</option>
                                    <option value="Oversize">Oversize</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label class="form-label fw-bold">📦 Magazzino (Quantità per Taglia/Colore)</label>
                            <div class="p-3 bg-light border rounded">
                                <div id="container-varianti">
                                    </div>
                                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="aggiungiVariante()">
                                    <i class="bi bi-plus-lg"></i> + Aggiungi Variante
                                </button>
                            </div>
                            <textarea name="magazzino" id="inputMagazzino" class="d-none"></textarea>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Principale</label>
                            <input type="file" name="immagine" class="form-control" accept="image/*" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Galleria Foto Extra</label>
                            <input type="file" name="galleria[]" class="form-control" accept="image/*" multiple>
                            <small class="text-muted">Puoi selezionare più foto contemporaneamente.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg fw-bold">Salva Prodotto</button>
                            <a href="<?= base_url('admin/prodotti') ?>" class="btn btn-outline-secondary">Annulla</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const container = document.getElementById('container-varianti');
    const inputFinale = document.getElementById('inputMagazzino');

    // Funzione per creare una nuova riga di variante
    function disegnaRiga(taglia = '', colore = '', quantita = 0) {
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 align-items-center riga-variante';
        div.innerHTML = `
            <div class="col-4">
                <select class="form-select form-select-sm inp-taglia" onchange="aggiornaJSON()" required>
                    <option value="">Taglia</option>
                    ${['XS','S','M','L','XL','XXL'].map(t => `<option value="${t}" ${t===taglia?'selected':''}>${t}</option>`).join('')}
                </select>
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm inp-colore" placeholder="Colore" value="${colore}" oninput="aggiornaJSON()" required>
            </div>
            <div class="col-3">
                <input type="number" class="form-control form-control-sm inp-qty" placeholder="Qta" value="${quantita}" min="0" oninput="aggiornaJSON()" required>
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="rimuoviRiga(this)">×</button>
            </div>
        `;
        container.appendChild(div);
        aggiornaJSON();
    }

    function aggiungiVariante() {
        disegnaRiga();
    }

    function rimuoviRiga(btn) {
        btn.closest('.riga-variante').remove();
        aggiornaJSON();
    }

    // Trasforma le righe in un oggetto JSON per il database
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

    // Crea la prima riga vuota all'avvio
    disegnaRiga();
</script>

<?= $this->endSection() ?>