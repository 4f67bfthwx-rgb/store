<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-cart3"></i> Il tuo Carrello</h2>

    <?php if (session()->getFlashdata('messaggio')): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= session()->getFlashdata('messaggio') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errore')): ?>
        <div class="alert alert-danger border-0 shadow-sm"><?= session()->getFlashdata('errore') ?></div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Prodotto</th>
                            <th>Dettagli</th>
                            <th class="text-center" style="width: 180px;">Quantità</th>
                            <th class="text-end">Prezzo Unit.</th>
                            <th class="text-end">Subtotale</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totaleGenerale = 0; ?>
                        <?php foreach ($items as $item): ?>
                            <?php 
                                $subtotale = $item['prezzo'] * $item['quantita']; 
                                $totaleGenerale += $subtotale;
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= base_url('uploads/prodotti/' . $item['immagine']) ?>" 
                                         class="rounded shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold"><?= esc($item['nome']) ?></h6>
                                    <span class="badge bg-light text-dark border mt-1">
                                        <?= $item['taglia'] ?> / <?= $item['colore'] ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= base_url('carrello/aggiorna') ?>" method="post" class="m-0">
                                        <input type="hidden" name="rowid" value="<?= $item['rowid'] ?>">
                                        
                                        <div class="input-group input-group-sm mx-auto" style="width: 130px;">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="this.parentNode.querySelector('input[type=number]').stepDown(); this.form.submit();">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            
                                            <input type="number" name="quantita" value="<?= $item['quantita'] ?>" 
                                                   class="form-control text-center fw-bold border-secondary" 
                                                   min="1" 
                                                   onchange="this.form.submit()" 
                                                   onwheel="this.blur()">
                                            
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="this.parentNode.querySelector('input[type=number]').stepUp(); this.form.submit();">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-end">€ <?= number_format($item['prezzo'], 2, ',', '.') ?></td>
                                <td class="text-end fw-bold text-primary">€ <?= number_format($subtotale, 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('carrello/rimuovi/' . $item['rowid']) ?>" class="btn btn-outline-danger btn-sm border-0">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold pt-3 pb-3">TOTALE GENERALE:</td>
                            <td class="text-end fw-bold text-success fs-5 pt-3 pb-3">€ <?= number_format($totaleGenerale, 2, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="<?= base_url('carrello/svuota') ?>" class="btn btn-link text-danger text-decoration-none" onclick="return confirm('Svuotare il carrello?')">
                <i class="bi bi-trash3"></i> Svuota carrello
            </a>
            <div>
                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary me-2">Continua lo Shopping</a>
                <a href="<?= base_url('carrello/checkout') ?>" class="btn btn-success px-4 fw-bold shadow-sm">Procedi al Checkout <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-3 text-muted">Il tuo carrello è vuoto.</h3>
            <a href="<?= base_url('/') ?>" class="btn btn-primary mt-3">Vai al catalogo</a>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Pulizia estetica per l'input numerico */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<?= $this->endSection() ?>