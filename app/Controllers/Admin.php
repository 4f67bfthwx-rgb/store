<?php

namespace App\Controllers;

use App\Models\ProdottiModel;
use App\Models\GalleriaModel;
use App\Models\OrdiniModel;
use App\Models\ConfigurazioniModel; 

class Admin extends BaseController
{
    // =========================================================================
    // 1. DASHBOARD E LISTA
    // =========================================================================
    public function dashboard()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');
        return view('admin/dashboard');
    }

    public function index()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');
        $model = new ProdottiModel();
        $data['prodotti'] = $model->orderBy('id', 'DESC')->findAll();
        return view('admin/lista_prodotti', $data);
    }

    // =========================================================================
    // 2. CREAZIONE PRODOTTO
    // =========================================================================
    public function crea()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');
        return view('admin/crea');
    }

    public function salva()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $model = new ProdottiModel();
        $galleriaModel = new GalleriaModel();

        $img = $this->request->getFile('immagine');
        $nomeFotoPrincipale = null; 

        if ($img && $img->isValid() && !$img->hasMoved()) {
            $nomeFotoPrincipale = $img->getRandomName(); 
            $img->move(FCPATH . 'uploads/prodotti', $nomeFotoPrincipale);
        }

        $data = [
            'nome'        => $this->request->getPost('nome'),
            'descrizione' => $this->request->getPost('descrizione'),
            'prezzo'      => $this->request->getPost('prezzo'),
            'vestibilita' => $this->request->getPost('vestibilita'),
            'magazzino'   => $this->request->getPost('magazzino'), 
            'immagine'    => $nomeFotoPrincipale 
        ];

        $prodId = $model->insert($data);

        $filesGalleria = $this->request->getFileMultiple('galleria');
        if ($filesGalleria) {
            foreach ($filesGalleria as $foto) {
                if ($foto->isValid() && !$foto->hasMoved()) {
                    $nomeExtra = $foto->getRandomName();
                    $foto->move(FCPATH . 'uploads/galleria', $nomeExtra);

                    $galleriaModel->save([
                        'prodotto_id' => $prodId,
                        'foto'        => $nomeExtra 
                    ]);
                }
            }
        }

        return redirect()->to('admin/prodotti')->with('success', 'Prodotto creato con successo');
    }

    // =========================================================================
    // 3. MODIFICA E AGGIORNAMENTO
    // =========================================================================
    public function modifica($id)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');
        $model = new ProdottiModel();
        $galleriaModel = new GalleriaModel();

        $data['prodotto'] = $model->find($id);
        $data['galleria'] = $galleriaModel->where('prodotto_id', $id)->findAll();

        return view('admin/modifica', $data);
    }

    public function aggiorna($id)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $model = new ProdottiModel();
        $galleriaModel = new GalleriaModel();
        $prodotto = $model->find($id);

        $img = $this->request->getFile('immagine');
        $nomeFotoPrincipale = $prodotto['immagine'];

        if ($img && $img->isValid() && !$img->hasMoved()) {
            if (!empty($prodotto['immagine']) && file_exists(FCPATH . 'uploads/prodotti/' . $prodotto['immagine'])) {
                @unlink(FCPATH . 'uploads/prodotti/' . $prodotto['immagine']);
            }
            $nomeFotoPrincipale = $img->getRandomName();
            $img->move(FCPATH . 'uploads/prodotti', $nomeFotoPrincipale);
        }

        $data = [
            'id'          => $id,
            'nome'        => $this->request->getPost('nome'),
            'descrizione' => $this->request->getPost('descrizione'),
            'prezzo'      => $this->request->getPost('prezzo'),
            'vestibilita' => $this->request->getPost('vestibilita'),
            'magazzino'   => $this->request->getPost('magazzino'),
            'immagine'    => $nomeFotoPrincipale
        ];
        $model->save($data);

        $filesGalleria = $this->request->getFileMultiple('galleria');
        if ($filesGalleria) {
            foreach ($filesGalleria as $foto) {
                if ($foto->isValid() && !$foto->hasMoved()) {
                    $nomeExtra = $foto->getRandomName();
                    $foto->move(FCPATH . 'uploads/galleria', $nomeExtra);
                    $galleriaModel->save([
                        'prodotto_id' => $id,
                        'foto'        => $nomeExtra
                    ]);
                }
            }
        }

        return redirect()->to('admin/prodotti')->with('success', 'Prodotto aggiornato');
    }

    // =========================================================================
    // 4. ELIMINAZIONE
    // =========================================================================
    public function elimina($id)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $model = new ProdottiModel();
        $galleriaModel = new GalleriaModel();
        
        $prodotto = $model->find($id);
        $galleria = $galleriaModel->where('prodotto_id', $id)->findAll();

        if (!empty($prodotto['immagine']) && file_exists(FCPATH . 'uploads/prodotti/' . $prodotto['immagine'])) {
            @unlink(FCPATH . 'uploads/prodotti/' . $prodotto['immagine']);
        }

        foreach ($galleria as $f) {
            if (file_exists(FCPATH . 'uploads/galleria/' . $f['foto'])) {
                @unlink(FCPATH . 'uploads/galleria/' . $f['foto']);
            }
        }

        $galleriaModel->where('prodotto_id', $id)->delete();
        $model->delete($id);

        return redirect()->to('admin/prodotti')->with('success', 'Prodotto e file eliminati');
    }

    public function eliminaFoto($idFoto)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $galleriaModel = new GalleriaModel();
        $foto = $galleriaModel->find($idFoto);
        
        if ($foto) {
            if (file_exists(FCPATH . 'uploads/galleria/' . $foto['foto'])) {
                @unlink(FCPATH . 'uploads/galleria/' . $foto['foto']);
            }
            
            $prodottoId = $foto['prodotto_id'];
            $galleriaModel->delete($idFoto);
            return redirect()->to('admin/modifica/' . $prodottoId)->with('success', 'Foto eliminata');
        }

        return redirect()->back();
    }

    // =========================================================================
    // 5. INVENTARIO E STOCK
    // =========================================================================
    public function inventario()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');
        $model = new ProdottiModel();
        $data['prodotti'] = $model->findAll();
        return view('admin/inventario', $data);
    }

    public function aggiorna_stock()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $id = $this->request->getPost('id');
        $taglia = $this->request->getPost('taglia');
        $colore = $this->request->getPost('colore');
        $nuovaQty = (int)$this->request->getPost('quantita');

        $model = new ProdottiModel();
        $prodotto = $model->find($id);

        if ($prodotto) {
            $magazzino = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];
            
            if (isset($magazzino[$taglia][$colore])) {
                $magazzino[$taglia][$colore] = $nuovaQty;
                
                $model->update($id, [
                    'magazzino' => json_encode($magazzino)
                ]);

                return redirect()->back()->with('success', 'Stock aggiornato con successo!');
            }
        }

        return redirect()->back()->with('error', 'Errore durante l\'aggiornamento.');
    }

    // =========================================================================
    // 6. STATISTICHE 
    // =========================================================================
    public function statistiche()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $ordiniModel = new OrdiniModel();
        
        $filtroGiorno = $this->request->getGet('giorno');
        $filtroMese   = $this->request->getGet('mese'); 
        $filtroAnno   = $this->request->getGet('anno') ?: date('Y'); 

        $query = $ordiniModel->where('stato !=', 'Annullato');

        if (!empty($filtroGiorno)) {
            $query->where("CAST(created_at AS DATE) = '$filtroGiorno'", null, false);
            $labelFiltro = "Giorno: " . date('d/m/Y', strtotime($filtroGiorno));
        } else {
            $query->where("EXTRACT(YEAR FROM created_at) = $filtroAnno", null, false);
            if (!empty($filtroMese)) {
                $query->where("EXTRACT(MONTH FROM created_at) = $filtroMese", null, false);
                $labelFiltro = "Mese: $filtroMese/$filtroAnno";
            } else {
                $labelFiltro = "Anno: $filtroAnno";
            }
        }

        $ordini = $query->findAll();

        $totaleAnno = $ordiniModel->where('stato !=', 'Annullato')
                                  ->where("EXTRACT(YEAR FROM created_at) = $filtroAnno", null, false)
                                  ->selectSum('totale')
                                  ->first();

        $stats = [
            'per_prodotto'    => [],
            'per_giorno'      => [],
            'totale_periodo'  => 0,
            'totale_anno'     => $totaleAnno['totale'] ?? 0,
            'filtro_attivo'   => $labelFiltro
        ];

        foreach ($ordini as $o) {
            $data = date('Y-m-d', strtotime($o['created_at']));
            $stats['totale_periodo'] += $o['totale'];
            $stats['per_giorno'][$data] = ($stats['per_giorno'][$data] ?? 0) + $o['totale'];

            $prodotti = json_decode($o['dettagli_prodotti'], true);
            if (is_array($prodotti)) {
                foreach ($prodotti as $p) {
                    $nome = $p['nome'];
                    if (!isset($stats['per_prodotto'][$nome])) {
                        $stats['per_prodotto'][$nome] = ['qty' => 0, 'incasso' => 0];
                    }
                    $stats['per_prodotto'][$nome]['qty'] += $p['quantita'];
                    $stats['per_prodotto'][$nome]['incasso'] += ($p['prezzo'] * $p['quantita']);
                }
            }
        }

        $mesiItaliani = [
            '1' => 'Gennaio', '2' => 'Febbraio', '3' => 'Marzo', '4' => 'Aprile',
            '5' => 'Maggio', '6' => 'Giugno', '7' => 'Luglio', '8' => 'Agosto',
            '9' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
        ];

        return view('admin/statistiche', [
            'stats'        => $stats,
            'filtroGiorno' => $filtroGiorno,
            'filtroMese'   => $filtroMese,
            'filtroAnno'   => $filtroAnno,
            'mesi'         => $mesiItaliani
        ]);
    }

    // =========================================================================
    // 7. GESTIONE ORDINI (Stock Restore + Email Aggiornata)
    // =========================================================================
    public function cambiaStato($id, $stato)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $ordiniModel = new OrdiniModel();
        $prodottiModel = new \App\Models\ProdottiModel(); 
        
        $ordine = $ordiniModel->find($id); 
        if (!$ordine) {
            return redirect()->back()->with('error', 'Ordine non trovato');
        }

        $statoDecodificato = urldecode($stato);

        // --- A. RIPRISTINO MAGAZZINO SE ANNULLATO ---
        if ($statoDecodificato == 'Annullato' && $ordine['stato'] != 'Annullato') {
            
            // Ripristina Prodotti Acquistati
            $prodotti = json_decode($ordine['dettagli_prodotti'], true);
            if (is_array($prodotti)) {
                foreach ($prodotti as $p) {
                    $prodDb = $prodottiModel->find($p['id']);
                    if ($prodDb) {
                        $stock = is_string($prodDb['magazzino']) ? json_decode($prodDb['magazzino'], true) : $prodDb['magazzino'];
                        if (isset($stock[$p['taglia']][$p['colore']])) {
                            $stock[$p['taglia']][$p['colore']] += $p['quantita'];
                            $prodottiModel->update($p['id'], ['magazzino' => json_encode($stock)]);
                        }
                    }
                }
            }

            // Ripristina Omaggi
            $omaggi = json_decode($ordine['omaggi'] ?? '[]', true);
            if (is_array($omaggi)) {
                foreach ($omaggi as $g) {
                    $prodDb = $prodottiModel->find($g['id']);
                    if ($prodDb) {
                        $stock = is_string($prodDb['magazzino']) ? json_decode($prodDb['magazzino'], true) : $prodDb['magazzino'];
                        if (isset($stock[$g['taglia']][$g['colore']])) {
                            $stock[$g['taglia']][$g['colore']] += $g['quantita'];
                            $prodottiModel->update($g['id'], ['magazzino' => json_encode($stock)]);
                        }
                    }
                }
            }
        }

        // --- B. AGGIORNAMENTO STATO DB ---
        $ordiniModel->update($id, [
            'stato' => $statoDecodificato
        ]);

        // --- C. INVIO EMAIL PROFESSIONALE (STATICA) ---
        $emailService = \Config\Services::email();
        // $configModel  = new ConfigurazioniModel(); // RIMOSSO PER EVITARE ERRORI DB
        
        // Dati Statici Negozio
        $nomeShop  = 'My Shop';
        $indShop   = 'Via Roma 10, Milano';
        $orariShop = 'Lun-Ven 9-18';

        $oggetto = "";
        $messaggioTitolo = "";
        $messaggioTesto = "";
        $coloreHeader = "#212529"; // Default scuro
        $inviareMail = false;

        switch ($statoDecodificato) {
            case 'Spedito':
                $inviareMail = true;
                $coloreHeader = "#0d6efd"; // Blu
                $oggetto = "🚚 Il tuo ordine #{$id} è partito!";
                $messaggioTitolo = "Ordine Spedito";
                $messaggioTesto = "Il tuo pacco è stato affidato al corriere. Riceverai presto aggiornamenti sulla consegna all'indirizzo indicato.";
                break;
            case 'Consegnato': 
                $inviareMail = true;
                $coloreHeader = "#198754"; // Verde
                $oggetto = "✅ Ordine #{$id} Consegnato / Pronto";
                $messaggioTitolo = "Ordine Completato";
                $messaggioTesto = "Il tuo ordine risulta consegnato o pronto per il ritiro presso la nostra sede.";
                break;
            case 'Annullato':
                $inviareMail = true;
                $coloreHeader = "#dc3545"; // Rosso
                $oggetto = "❌ Ordine #{$id} Annullato";
                $messaggioTitolo = "Ordine Annullato";
                $messaggioTesto = "Come richiesto o per problemi di stock, il tuo ordine è stato annullato. Se hai già pagato, verrai rimborsato a breve.";
                break;
        }

        if ($inviareMail && !empty($ordine['email'])) {
            
            $messaggioCompleto = "
            <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;\">
                <div style=\"background-color: {$coloreHeader}; color: #ffffff; padding: 20px; text-align: center;\">
                    <h2 style='margin:0;'>{$messaggioTitolo}</h2>
                </div>
                <div style=\"padding: 30px; background-color: #ffffff; color: #333;\">
                    <p>Ciao <strong>{$ordine['nome_cliente']}</strong>,</p>
                    <p style='font-size: 16px;'>{$messaggioTesto}</p>
                    
                    <hr style='margin: 30px 0; border: 0; border-top: 1px solid #eee;'>
                    
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 14px;'>
                        <strong>Hai bisogno di aiuto?</strong><br>
                        Passa a trovarci o contattaci:<br>
                        📍 {$indShop}<br>
                        🕒 {$orariShop}
                    </div>
                </div>
                <div style=\"background-color: #eee; padding: 15px; text-align: center; font-size: 11px; color: #777;\">
                    Ordine #{$id} - {$nomeShop}
                </div>
            </div>";

            $emailService->setTo($ordine['email']);
            $emailService->setSubject($oggetto);
            $emailService->setMessage($messaggioCompleto);
            @$emailService->send();
        }

        return redirect()->back()->with('success', "Stato ordine #$id aggiornato a: $statoDecodificato");
    }

    // =========================================================================
    // 8. CONFIGURAZIONI (INFO NEGOZIO + FEDELTÀ)
    // =========================================================================

    public function salvaConfigurazione()
    {
        // Funzione svuotata perché ora i dati sono statici
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');
        return redirect()->back()->with('success', 'Configurazioni aggiornate (Modalità Statica).');
    }

    // Aggiungi Regola Fedeltà Multi-Livello (CORRETTA CON WHERE)
    public function aggiungiRegolaFedelta()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $punti  = (int) $this->request->getPost('punti');
        $sconto = (int) $this->request->getPost('sconto');

        if ($punti <= 0 || $sconto <= 0) {
            return redirect()->back()->with('error', 'Inserisci valori validi per punti e sconto.');
        }

        $configModel = new \App\Models\ConfigurazioniModel();
        
        // CORREZIONE: usiamo where() e first() per evitare errori di ID
        $dataDb = $configModel->where('chiave', 'regole_fedelta')->first();
        $regole = [];

        if ($dataDb) {
            $regole = json_decode($dataDb['valore'], true) ?? [];
        }

        $regole[] = [
            'punti' => $punti,
            'sconto' => $sconto
        ];

        usort($regole, function($a, $b) {
            return $b['punti'] - $a['punti'];
        });

        if ($dataDb) {
            $configModel->update($dataDb['id'], ['valore' => json_encode($regole)]);
        } else {
            $configModel->insert(['chiave' => 'regole_fedelta', 'valore' => json_encode($regole)]);
        }

        return redirect()->to('admin/dashboard')->with('success', "Nuovo livello fedeltà aggiunto.");
    }

    public function rimuoviRegolaFedelta($index)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $configModel = new \App\Models\ConfigurazioniModel();
        
        // CORREZIONE: usiamo where()
        $dataDb = $configModel->where('chiave', 'regole_fedelta')->first();
        
        if ($dataDb) {
            $regole = json_decode($dataDb['valore'], true) ?? [];
            
            if (isset($regole[$index])) {
                unset($regole[$index]);
                $regole = array_values($regole);
                
                $configModel->update($dataDb['id'], ['valore' => json_encode($regole)]);
            }
        }

        return redirect()->to('admin/dashboard')->with('success', 'Livello fedeltà rimosso.');
    }

    // =========================================================================
    // 9. AGGIUNGI OMAGGIO
    // =========================================================================
    public function aggiungiOmaggio()
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $idOrdine    = $this->request->getPost('id_ordine');
        $idProdotto  = $this->request->getPost('id_prodotto');
        $taglia      = $this->request->getPost('taglia');
        $colore      = $this->request->getPost('colore');
        $qtyOmaggio  = (int) $this->request->getPost('quantita');

        $ordiniModel   = new OrdiniModel();
        $prodottiModel = new \App\Models\ProdottiModel();

        $prodotto = $prodottiModel->find($idProdotto);
        if (!$prodotto) {
            return redirect()->back()->with('error', 'Prodotto non trovato.');
        }

        $stock = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];

        if (!isset($stock[$taglia][$colore])) {
            return redirect()->back()->with('error', "Variante non trovata ($taglia - $colore).");
        }
        if ($stock[$taglia][$colore] < $qtyOmaggio) {
            return redirect()->back()->with('error', "Quantità insufficiente in magazzino.");
        }

        $stock[$taglia][$colore] -= $qtyOmaggio;
        $prodottiModel->update($idProdotto, ['magazzino' => json_encode($stock)]);

        $ordine = $ordiniModel->find($idOrdine);
        
        $omaggiAttuali = [];
        if (!empty($ordine['omaggi'])) {
            $decoded = json_decode($ordine['omaggi'], true);
            if (is_array($decoded)) {
                $omaggiAttuali = $decoded;
            }
        }

        $omaggiAttuali[] = [
            'id'      => $prodotto['id'],
            'nome'    => $prodotto['nome'],
            'taglia'  => $taglia,
            'colore'  => $colore,
            'quantita'=> $qtyOmaggio,
            'data_ins'=> date('d/m/Y')
        ];

        $ordiniModel->update($idOrdine, ['omaggi' => json_encode($omaggiAttuali)]);

        return redirect()->back()->with('success', "Omaggio aggiunto e stock aggiornato!");
    }
}