<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">📊 Report Vendite</h2>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">Torna alla Dashboard</a>
    </div>

    <div class="card shadow-sm border-0 mb-4 bg-light text-dark">
        <div class="card-body">
            <form action="<?= base_url('admin/statistiche') ?>" method="get" id="formFiltri" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted text-uppercase">1. Per Giorno</label>
                    <input type="date" id="filtroGiorno" name="giorno" class="form-control" value="<?= $filtroGiorno ?>">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted text-uppercase">2. Per Mese</label>
                    <select id="filtroMese" name="mese" class="form-select">
                        <option value="">-- Seleziona Mese --</option>
                        <?php foreach($mesi as $num => $nome): ?>
                            <option value="<?= $num ?>" <?= $filtroMese == $num ? 'selected' : '' ?>><?= $nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase">3. Per Anno</label>
                    <select id="filtroAnno" name="anno" class="form-select">
                        <option value="">-- Anno --</option>
                        <?php for($y = 2024; $y <= date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= $filtroAnno == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold flex-grow-1 shadow-sm">Applica Filtro</button>
                    <a href="<?= base_url('admin/statistiche') ?>" class="btn btn-outline-secondary">Reset Tutto</a>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm py-2 mb-4">
        <i class="bi bi-calendar-check-fill me-2"></i> Report Attuale: <strong><?= esc($stats['filtro_attivo']) ?></strong>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card bg-primary text-white shadow-sm border-0 p-4">
                <small class="text-uppercase opacity-75 fw-bold">Incasso Periodo Selezionato</small>
                <h2 class="fw-bold m-0">€ <?= number_format($stats['totale_periodo'], 2, ',', '.') ?></h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-dark text-white shadow-sm border-0 p-4">
                <small class="text-uppercase opacity-75 fw-bold text-warning">Totale Incassato Anno <?= $filtroAnno ?: date('Y') ?></small>
                <h2 class="fw-bold m-0">€ <?= number_format($stats['totale_anno'], 2, ',', '.') ?></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-trophy-fill text-warning"></i> Prodotti nel periodo</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Prodotto</th><th class="text-center">Quantità</th><th class="text-end pe-4">Totale</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($stats['per_prodotto'])): foreach($stats['per_prodotto'] as $nome => $d): ?>
                            <tr>
                                <td class="ps-3"><?= esc($nome) ?></td>
                                <td class="text-center"><?= $d['qty'] ?></td>
                                <td class="text-end fw-bold text-success pe-4">€ <?= number_format($d['incasso'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">Nessun dato per questo filtro.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-graph-up-arrow text-primary"></i> Dettaglio Giornaliero</div>
                <div class="card-body">
                    <?php if(!empty($stats['per_giorno'])): krsort($stats['per_giorno']); foreach($stats['per_giorno'] as $giorno => $tot): ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="small"><?= date('d/m/Y', strtotime($giorno)) ?></span>
                            <span class="fw-bold text-primary">€ <?= number_format($tot, 2, ',', '.') ?></span>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-muted text-center py-4">Nessuna vendita trovata.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Seleziono gli elementi
    const inputGiorno = document.getElementById('filtroGiorno');
    const selectMese = document.getElementById('filtroMese');
    const selectAnno = document.getElementById('filtroAnno');

    // Se cambio il GIORNO -> resetto Mese e Anno
    inputGiorno.addEventListener('change', function() {
        if(this.value !== "") {
            selectMese.value = "";
            selectAnno.value = "";
        }
    });

    // Se cambio il MESE -> resetto Giorno
    selectMese.addEventListener('change', function() {
        if(this.value !== "") {
            inputGiorno.value = "";
        }
    });

    // Se cambio l'ANNO -> resetto Giorno
    selectAnno.addEventListener('change', function() {
        if(this.value !== "") {
            inputGiorno.value = "";
        }
    });
</script>

<?= $this->endSection() ?>