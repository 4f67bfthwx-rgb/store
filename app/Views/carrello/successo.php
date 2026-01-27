<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5 text-center">
    <div class="card border-0 shadow-sm p-5">
        <div class="mb-4">
            <div style="font-size: 80px;">🛍️</div>
        </div>
        
        <h1 class="display-4 fw-bold text-success mb-3">Ordine Prenotato!</h1>
        <p class="lead text-muted">Grazie <strong><?= esc($email_cliente) ?></strong>, abbiamo messo da parte i tuoi articoli.</p>
        
        <div class="alert alert-warning d-inline-block mt-3">
            <strong>📍 Dove ritirare:</strong><br>
            Via del Corso 123, Roma<br>
            Orari: 09:00 - 20:00
        </div>

        <hr class="my-4">
        
        <p>Ricorda di passare in cassa per completare il pagamento.</p>
        
        <a href="<?= base_url('/') ?>" class="btn btn-primary btn-lg px-5 fw-bold mt-3">
            Torna allo Shop
        </a>
    </div>
</div>

<?= $this->endSection() ?>