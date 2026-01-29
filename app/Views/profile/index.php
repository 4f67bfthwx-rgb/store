<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold">👋 Ciao, <?= esc($user['nome']) ?></h1>
            <p class="text-muted">Benvenuto nella tua area personale.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="card bg-warning text-dark border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold mb-1 opacity-75">Il tuo Saldo Punti</h6>
                    <h2 class="mb-0 fw-bold display-6">⭐ <?= $user['punti_fedelta'] ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i> I tuoi Ordini</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">N. Ordine</th>
                            <th>Data</th>
                            <th>Articoli</th>
                            <th>Totale</th>
                            <th>Stato</th>
                            <th>Dettagli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($ordini)): ?>
                            <?php foreach($ordini as $ordine): ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#<?= $ordine['id'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($ordine['created_at'])) ?></td>
                                    <td>
                                        <?php 
                                            $prodotti = json_decode($ordine['dettagli_prodotti'], true);
                                            $count = is_array($prodotti) ? count($prodotti) : 0;
                                            // Mostra i primi 2 nomi per brevità
                                            if ($count > 0) {
                                                echo esc($prodotti[0]['nome']);
                                                if ($count > 1) echo " + altri " . ($count - 1);
                                            }
                                        ?>
                                    </td>
                                    <td class="fw-bold">€ <?= number_format($ordine['totale'], 2, ',', '.') ?></td>
                                    <td>
                                        <?php 
                                            $coloreBadge = 'secondary';
                                            switch($ordine['stato']) {
                                                case 'In lavorazione': $coloreBadge = 'warning text-dark'; break;
                                                case 'Spedito':        $coloreBadge = 'primary'; break;
                                                case 'Consegnato':     $coloreBadge = 'success'; break;
                                                case 'Annullato':      $coloreBadge = 'danger'; break;
                                            }
                                        ?>
                                        <span class="badge bg-<?= $coloreBadge ?>"><?= esc($ordine['stato']) ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#ordineModal<?= $ordine['id'] ?>">
                                            Vedi <i class="bi bi-eye"></i>
                                        </button>

                                        <div class="modal fade" id="ordineModal<?= $ordine['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Dettaglio Ordine #<?= $ordine['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($ordine['created_at'])) ?></p>
                                                        <p><strong>Indirizzo:</strong> <?= esc($ordine['indirizzo']) ?></p>
                                                        <hr>
                                                        <h6>Prodotti:</h6>
                                                        <ul class="list-group list-group-flush mb-3">
                                                            <?php foreach($prodotti as $p): ?>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                                    <div>
                                                                        <strong><?= esc($p['nome']) ?></strong>
                                                                        <br><small class="text-muted"><?= esc($p['taglia']) ?> / <?= esc($p['colore']) ?></small>
                                                                    </div>
                                                                    <span>x<?= esc($p['quantita']) ?></span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        
                                                        <?php if(!empty($ordine['omaggi'])): ?>
                                                            <div class="alert alert-success py-2 small">
                                                                <i class="bi bi-gift-fill"></i> <strong>Note/Omaggi:</strong><br> 
                                                                <?php 
                                                                    $omaggi = json_decode($ordine['omaggi'], true);
                                                                    if(is_array($omaggi)) {
                                                                        foreach($omaggi as $g) {
                                                                            echo "• " . esc($g['nome']) . " (" . $g['taglia'] . ")<br>";
                                                                        }
                                                                    } else {
                                                                        echo esc($ordine['omaggi']);
                                                                    }
                                                                ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <h4 class="text-end mt-3">Totale: € <?= number_format($ordine['totale'], 2, ',', '.') ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Non hai ancora effettuato ordini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>