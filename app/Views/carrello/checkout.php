<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <h2 class="mb-4">📦 Checkout</h2>
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="<?= base_url('carrello/conferma') ?>" method="post">
                        
                        <h5 class="mb-3">Dati Spedizione</h5>
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome_cliente" class="form-control" required value="<?= session()->get('nome') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= session()->get('email') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo" class="form-control" placeholder="Via, numero civico..." required>
                        </div>
                        <hr class="my-4">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold">CONFERMA E PAGA</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h5>Riepilogo</h5>
                    <?php 
                        $tot = 0;
                        foreach($items as $i) { $tot += $i['prezzo'] * $i['quantita']; }
                    ?>
                    <h3 class="fw-bold mt-3">€ <?= number_format($tot, 2) ?></h3>
                    <small class="text-muted">*Sconti e punti calcolati alla conferma</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>