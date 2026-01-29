<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
       .alert ul {
        margin-bottom: 0;
        padding-left: 20px; 
    }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4 text-center">Registrati</h3>

                    <?php if (session()->get('errors')): ?>
                        <div class="alert alert-danger">
                            <ul>
                            <?php foreach (session()->get('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" value="<?= old('nome') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Conferma Password</label>
                                <input type="password" name="confpassword" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success fw-bold">Crea Account</button>
                        </div>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        Hai già un account? <a href="<?= base_url('login') ?>">Accedi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>