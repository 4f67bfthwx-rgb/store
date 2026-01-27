<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">

    <style>
        .cursor-zoom { cursor: pointer !important; transition: transform 0.3s ease; }
        .cursor-zoom:hover { transform: scale(1.02); opacity: 0.95; }
        .modal-img { width: 100%; height: auto; object-fit: contain; max-height: 90vh; }
        .admin-badge { letter-spacing: 1px; }
        /* Animazione per l'avviso di quantità */
        .shake { animation: shake 0.5s; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($prodotto['nome']) ?></li>
      </ol>
    </nav>

    <div class="row mt-4 align-items-start">
        
        <div class="col-md-6 mb-4">
            <div id="carouselProdotto" class="carousel slide shadow rounded overflow-hidden" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselProdotto" data-bs-slide-to="0" class="active"></button>
                    <?php if (!empty($galleria)): foreach($galleria as $index => $foto): ?>
                        <button type="button" data-bs-target="#carouselProdotto" data-bs-slide-to="<?= $index + 1 ?>"></button>
                    <?php endforeach; endif; ?>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <?php 
                            // Percorso immagine principale dal server
                            $src = !empty($prodotto['immagine']) 
                                ? base_url('uploads/prodotti/' . $prodotto['immagine']) 
                                : 'https://via.placeholder.com/600x600?text=No+Image';
                        ?>
                        <img src="<?= $src ?>" class="d-block w-100 cursor-zoom" style="height: 500px; object-fit: cover;" onclick="apriZoom(this.src)">
                    </div>
                    <?php if (!empty($galleria)): foreach($galleria as $foto): ?>
                        <div class="carousel-item">
                            <img src="<?= base_url('uploads/galleria/' . $foto['foto']) ?>" class="d-block w-100 cursor-zoom" style="height: 500px; object-fit: cover;" onclick="apriZoom(this.src)">
                        </div>
                    <?php endforeach; endif; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselProdotto" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselProdotto" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <?php if (session()->get('ruolo') == 'admin'): ?>
                <span class="badge bg-danger mb-2 admin-badge shadow-sm"><i class="bi bi-shield-lock-fill"></i> VISTA AMMINISTRATORE</span>
            <?php endif; ?>

            <h1 class="display-4 fw-bold"><?= esc($prodotto['nome']) ?></h1>
            <h2 class="text-primary mb-3">€ <?= number_format($prodotto['prezzo'], 2, ',', '.') ?></h2>
            <div class="mb-4"><span class="badge bg-light text-dark border p-2">Vestibilità: <?= esc($prodotto['vestibilita']) ?></span></div>
            <p class="lead text-muted"><?= nl2br(esc($prodotto['descrizione'])) ?></p>
            <hr>

            <?php 
                // Decodifica magazzino per calcolo totale
                $magazzino = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];
                $magazzino = $magazzino ?? [];
                $totalePezzi = 0;
                foreach($magazzino as $t => $cols) {
                    if(is_array($cols)) {
                        foreach($cols as $q) $totalePezzi += (int)$q;
                    }
                }
            ?>

            <?php if (session()->get('ruolo') == 'admin'): ?>
                <div class="card border-danger shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-danger mb-3"><i class="bi bi-gear-fill"></i> Pannello di Controllo</h5>
                    <div class="mb-4">
                        <label class="fw-bold mb-2">📦 Giacenza Magazzino:</label>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light"><tr><th>Taglia</th><th>Colore</th><th>Q.tà</th></tr></thead>
                            <tbody>
                                <?php foreach($magazzino as $t => $cols): foreach($cols as $c => $q): ?>
                                    <tr><td><?= $t ?></td><td><?= $c ?></td><td class="<?= $q <= 0 ? 'text-danger fw-bold' : '' ?>"><?= $q ?></td></tr>
                                <?php endforeach; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('admin/modifica/' . $prodotto['id']) ?>" class="btn btn-warning fw-bold text-dark"><i class="bi bi-pencil-square"></i> MODIFICA</a>
                    </div>
                </div>

            <?php else: ?>
                
                <?php if ($totalePezzi > 0): ?>
                    <div class="card bg-light border-0 p-4 shadow-sm">
                        <form action="<?= base_url('carrello/aggiungi') ?>" method="post" id="formAcquisto">
                            <input type="hidden" name="id" value="<?= $prodotto['id'] ?>">
                            <input type="hidden" name="variante" id="inputVarianteFinale">

                            <div class="mb-3">
                                <label class="fw-bold mb-1 small text-uppercase">1. Taglia</label>
                                <select id="selectTaglia" class="form-select shadow-sm" onchange="aggiornaColori()" required>
                                    <option value="">-- Scegli Taglia --</option>
                                    <?php foreach($magazzino as $taglia => $colori): ?>
                                        <?php if(is_array($colori) && array_sum($colori) > 0): ?>
                                            <option value="<?= $taglia ?>"><?= $taglia ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold mb-1 small text-uppercase">2. Colore</label>
                                <select id="selectColore" class="form-select shadow-sm" disabled onchange="aggiornaQuantita()" required>
                                    <option value="">-- Prima scegli la taglia --</option>
                                </select>
                                <div id="msgDisponibilita" class="fw-bold text-muted mt-2" style="font-size: 0.85rem; min-height: 20px;">&nbsp;</div>
                            </div>

                            <div class="d-flex align-items-end gap-2">
                                <div style="width: 80px;">
                                    <label class="fw-bold mb-1 small text-uppercase">Q.tà</label>
                                    <input type="number" name="quantita" id="inputQuantita" 
                                        class="form-control text-center fw-bold shadow-sm" 
                                        value="1" min="1" max="1" disabled 
                                        oninput="validaMaxQty(this)">
                                </div>
                                <div class="flex-grow-1">
                                    <button type="submit" id="btnAggiungi" class="btn btn-success w-100 fw-bold shadow-sm" disabled style="height: 38px;">
                                        AGGIUNGI AL CARRELLO 🛒
                                    </button>
                                </div>
                            </div>
                            
                            <div id="alertOltreMax" class="alert alert-danger py-2 px-3 small d-none mt-3 shadow-sm border-0">
                                <i class="bi bi-exclamation-triangle-fill"></i> Quantità massima superata!
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger fw-bold text-center p-3 shadow-sm">❌ PRODOTTO ESAURITO</div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    <?php if (session()->get('ruolo') != 'admin'): ?>
    const magazzino = <?= json_encode($magazzino) ?>;

    function aggiornaColori() {
        const tagliaScelta = document.getElementById('selectTaglia').value;
        const selectColore = document.getElementById('selectColore');
        const btn = document.getElementById('btnAggiungi');
        const inputQty = document.getElementById('inputQuantita');
        
        selectColore.innerHTML = '<option value="">-- Scegli Colore --</option>';
        selectColore.disabled = true;
        btn.disabled = true;
        inputQty.disabled = true;
        document.getElementById('msgDisponibilita').innerText = "";
        document.getElementById('alertOltreMax').classList.add('d-none');

        if (tagliaScelta && magazzino[tagliaScelta]) {
            selectColore.disabled = false;
            const colori = magazzino[tagliaScelta];
            for (const [colore, qty] of Object.entries(colori)) {
                if (qty > 0) {
                    let option = document.createElement("option");
                    option.value = colore;
                    option.text = colore;
                    option.setAttribute('data-qty', qty);
                    selectColore.add(option);
                }
            }
        }
    }

    function aggiornaQuantita() {
        const tagliaScelta = document.getElementById('selectTaglia').value;
        const selectColore = document.getElementById('selectColore');
        const inputQty = document.getElementById('inputQuantita');
        const btn = document.getElementById('btnAggiungi');
        const inputVariante = document.getElementById('inputVarianteFinale');

        const selectedOption = selectColore.options[selectColore.selectedIndex];
        const qtyDisponibile = parseInt(selectedOption.getAttribute('data-qty'));

        if (qtyDisponibile) {
            inputQty.max = qtyDisponibile;
            inputQty.value = 1;
            inputQty.disabled = false;
            document.getElementById('msgDisponibilita').innerText = "Disponibili: " + qtyDisponibile + " pz";
            inputVariante.value = tagliaScelta + "-" + selectedOption.value;
            btn.disabled = false;
        }
    }

    // NUOVA FUNZIONE DI CONTROLLO ISTANTANEO
    function validaMaxQty(input) {
        const max = parseInt(input.max);
        const val = parseInt(input.value);
        const alertBox = document.getElementById('alertOltreMax');

        if (val > max) {
            input.value = max; // Riporta al massimo disponibile
            alertBox.classList.remove('d-none');
            input.classList.add('is-invalid', 'shake');
            
            // Rimuove l'effetto scossa dopo 500ms
            setTimeout(() => { input.classList.remove('shake'); }, 500);
            
            // Nasconde l'alert dopo 3 secondi
            setTimeout(() => { alertBox.classList.add('d-none'); input.classList.remove('is-invalid'); }, 3000);
        } else {
            alertBox.classList.add('d-none');
            input.classList.remove('is-invalid');
        }
    }
    <?php endif; ?>

    function apriZoom(imageSrc) {
        document.getElementById('zoomImage').src = imageSrc;
        var myModal = new bootstrap.Modal(document.getElementById('zoomModal'));
        myModal.show();
    }
</script>

<?= $this->endSection() ?>