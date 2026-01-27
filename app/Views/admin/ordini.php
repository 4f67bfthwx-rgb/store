<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">📦 Gestione Ordini</h2>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Dashboard Admin
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Cliente / Località</th>
                            <th>Dettaglio Prodotti</th>
                            <th class="text-center">Stato</th>
                            <th>Totale</th>
                            <th>Data Ordine</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($ordini)): ?>
                            <?php foreach($ordini as $o): ?>
                            <tr>
                                <td class="fw-bold">#<?= $o['id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($o['nome_cliente']) ?></div>
                                    <div class="small text-muted"><?= esc($o['email']) ?></div>
                                    <span class="badge bg-light text-dark border mt-1"><?= esc($o['citta']) ?></span>
                                </td>
                                <td>
                                    <?php 
                                        $prodotti = json_decode($o['dettagli_prodotti'], true);
                                        
                                        if(is_array($prodotti) && !empty($prodotti)): 
                                            foreach($prodotti as $p): ?>
                                                <div class="mb-2 pb-1 border-bottom border-light">
                                                    <a href="<?= base_url('prodotto/' . $p['id']) ?>" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                        <i class="bi bi-link-45deg"></i> <?= esc($p['nome']) ?>
                                                    </a>
                                                    <br>
                                                    <small class="text-secondary">
                                                        Taglia: <strong><?= $p['taglia'] ?></strong> | 
                                                        Colore: <strong><?= $p['colore'] ?></strong> | 
                                                        Qtà: <strong><?= $p['quantita'] ?></strong>
                                                    </small>
                                                </div>
                                            <?php endforeach; 
                                        else: ?>
                                            <span class="text-danger small italic">Errore dati</span>
                                        <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        // Gestione colori Badge in base allo stato
                                        $badgeClass = 'warning text-dark'; 
                                        if ($o['stato'] == 'Pronto') $badgeClass = 'info text-white';
                                        if ($o['stato'] == 'Consegnato') $badgeClass = 'success';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                        <?= esc($o['stato']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-success">
                                    € <?= number_format($o['totale'], 2, ',', '.') ?>
                                </td>
                                <td class="small">
                                    <?= date('d/m/Y', strtotime($o['created_at'])) ?><br>
                                    <span class="text-muted"><?= date('H:i', strtotime($o['created_at'])) ?></span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-dark dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            Aggiorna
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><h6 class="dropdown-header">Cambia stato in:</h6></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/cambia_stato/'.$o['id'].'/In lavorazione') ?>">⏳ In lavorazione</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/cambia_stato/'.$o['id'].'/Pronto') ?>">✅ Pronto al ritiro</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/cambia_stato/'.$o['id'].'/Consegnato') ?>">📦 Consegnato</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x display-4 d-block mb-2"></i>
                                    Non ci sono ordini da visualizzare.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>