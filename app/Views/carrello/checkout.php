<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Il tuo carrello</span>
                <span class="badge bg-primary rounded-pill"><?= count($items) ?></span>
            </h4>
            <ul class="list-group mb-3 shadow-sm">
                <?php $totale = 0; ?>
                <?php foreach($items as $item): ?>
                    <?php $totale += $item['prezzo'] * $item['quantita']; ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?= esc($item['nome']) ?></h6>
                            <small class="text-muted"><?= $item['taglia'] ?> / <?= $item['colore'] ?> (x<?= $item['quantita'] ?>)</small>
                        </div>
                        <span class="text-muted">€ <?= number_format($item['prezzo'] * $item['quantita'], 2) ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between bg-light fw-bold">
                    <span>Totale da Pagare</span>
                    <strong>€ <?= number_format($totale, 2) ?></strong>
                </li>
            </ul>
        </div>

        <div class="col-md-8 order-md-1">
            <div class="alert alert-info border-0 shadow-sm">
                <i class="bi bi-shop"></i> <strong>Nota Bene:</strong> Il pagamento avverrà direttamente al ritiro in negozio.
            </div>

            <h4 class="mb-3 fw-bold">📦 Dati per il Ritiro</h4>
            
            <form action="<?= base_url('checkout/conferma') ?>" method="post">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nome e Cognome</label>
                        <input type="text" class="form-control" name="nome_cliente" 
                               value="<?= esc(session()->get('nome') ?? '') ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Email (per la conferma)</label>
                        <input type="email" class="form-control" name="email" placeholder="tu@esempio.com" 
                               value="<?= esc(session()->get('email') ?? '') ?>" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Città di provenienza</label>
                        <input type="text" class="form-control" name="citta" required>
                    </div>
                    
                    <input type="hidden" name="indirizzo" value="Ritiro in Sede">
                </div>

                <hr class="my-4">

                <h4 class="mb-3">Metodo di Pagamento</h4>
                <div class="my-3">
                    <div class="form-check p-3 border rounded bg-light shadow-sm">
                        <input id="cash" name="paymentMethod" type="radio" class="form-check-input" checked>
                        <label class="form-check-label fw-bold" for="cash">
                            💵 Pagamento in cassa al ritiro
                        </label>
                        <div class="text-muted small mt-1">Prenota ora, paga quando vieni a trovarci.</div>
                    </div>
                </div>

                <hr class="my-4">

                <button class="w-100 btn btn-success btn-lg fw-bold shadow" type="submit">
                    CONFERMA PRENOTAZIONE (€ <?= number_format($totale, 2) ?>)
                </button>
                <a href="<?= base_url('carrello') ?>" class="w-100 btn btn-link mt-2">Modifica il carrello</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>