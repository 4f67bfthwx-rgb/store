<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <h2 class="fw-bold mb-4">📦 Gestione Rapida Inventario</h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">Prodotto</th>
                        <th>Taglia</th>
                        <th>Colore</th>
                        <th class="text-center" style="width: 150px;">Quantità</th>
                        <th class="text-center">Azione</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($prodotti as $p): 
                        $mag = is_string($p['magazzino']) ? json_decode($p['magazzino'], true) : $p['magazzino'];
                        if (is_array($mag)):
                            foreach($mag as $taglia => $colori):
                                foreach($colori as $colore => $qty):
                    ?>
                    <tr>
                        <?php if ($taglia === array_key_first($mag) && $colore === array_key_first($colori)): ?>
                            <td rowspan="<?= count($colori) * count($mag) ?>" class="ps-4 border-end">
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url('uploads/prodotti/'.$p['immagine']) ?>" width="50" height="50" class="rounded me-3" style="object-fit: cover;">
                                    <strong><?= esc($p['nome']) ?></strong>
                                </div>
                            </td>
                        <?php endif; ?>
                        
                        <td class="fw-bold text-muted"><?= $taglia ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $colore ?></span></td>
                        
                        <td class="text-center">
                            <form action="<?= base_url('admin/aggiorna_stock') ?>" method="post" class="d-flex justify-content-center">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="taglia" value="<?= $taglia ?>">
                                <input type="hidden" name="colore" value="<?= $colore ?>">
                                <input type="number" name="quantita" value="<?= $qty ?>" 
                                       class="form-control form-control-sm text-center fw-bold <?= $qty <= 2 ? 'border-danger text-danger' : 'border-success' ?>" 
                                       style="width: 70px;" min="0">
                        </td>
                        <td class="text-center">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-save"></i> Salva
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                                endforeach; 
                            endforeach; 
                        endif;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>