<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">🛡️ Crea Nuovo Admin</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">Attenzione: L'utente che stai creando avrà accesso completo alla gestione del sito.</p>
                    
                    <form action="<?= base_url('admin/salva-admin') ?>" method="post">
                        
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold">Crea Admin</button>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary w-100 mt-2">Annulla</a>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>