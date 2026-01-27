<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">👕 Gestione Prodotti</h2>
        <div>
            <a href="<?= base_url('admin/crea') ?>" class="btn btn-success fw-bold me-2">+ Nuovo Prodotto</a>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Torna alla Dashboard</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success shadow-sm border-0"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 100px;">Foto</th>
                        <th>Nome</th>
                        <th>Prezzo</th>
                        <th class="text-end px-4">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prodotti)): ?>
                        <?php foreach ($prodotti as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td>
                                    <?php if ($p['immagine']): ?>
                                        <img src="<?= base_url('uploads/prodotti/' . $p['immagine']) ?>" width="50" height="50" style="object-fit: cover;" class="rounded shadow-sm">
                                    <?php else: ?>
                                        <div class="bg-light rounded text-center text-muted" style="width: 50px; height: 50px; line-height: 50px; font-size: 10px;">No img</div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= esc($p['nome']) ?></td>
                                <td class="text-success fw-bold">€ <?= number_format($p['prezzo'], 2, ',', '.') ?></td>
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="<?= base_url('admin/modifica/' . $p['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Modifica
                                        </a>
                                        <a href="<?= base_url('admin/elimina/' . $p['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo prodotto e tutte le sue immagini?')">
                                            <i class="bi bi-trash"></i> Elimina
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nessun prodotto trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>