<?php

namespace App\Controllers;

use App\Models\ProdottiModel;
use App\Models\GalleriaModel;

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

        // 1. Gestione Foto Principale
        $img = $this->request->getFile('immagine');
        $nomeFotoPrincipale = null;

        if ($img && $img->isValid() && !$img->hasMoved()) {
            $nomeFotoPrincipale = $img->getRandomName(); // Genera nome univoco
            $img->move(FCPATH . 'uploads/prodotti', $nomeFotoPrincipale); // Sposta il file
        }

        // 2. Salvataggio Dati Prodotto
        $data = [
            'nome'        => $this->request->getPost('nome'),
            'descrizione' => $this->request->getPost('descrizione'),
            'prezzo'      => $this->request->getPost('prezzo'),
            'vestibilita' => $this->request->getPost('vestibilita'),
            'magazzino'   => $this->request->getPost('magazzino'),
            'immagine'    => $nomeFotoPrincipale // Solo il nome del file
        ];

        $prodId = $model->insert($data);

        // 3. Gestione Galleria (Foto multiple)
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

        // 1. Sostituzione Foto Principale (se caricata una nuova)
        $img = $this->request->getFile('immagine');
        $nomeFotoPrincipale = $prodotto['immagine'];

        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Cancella il vecchio file fisico se esiste
            if (!empty($prodotto['immagine']) && file_exists(FCPATH . 'uploads/prodotti/' . $prodotto['immagine'])) {
                unlink(FCPATH . 'uploads/prodotti/' . $prodotto['immagine']);
            }
            // Carica il nuovo
            $nomeFotoPrincipale = $img->getRandomName();
            $img->move(FCPATH . 'uploads/prodotti', $nomeFotoPrincipale);
        }

        // 2. Aggiornamento DB
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

        // 3. Nuove foto in Galleria
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

        // 1. Cancella fisicamente la foto principale
        if (!empty($prodotto['immagine']) && file_exists(FCPATH . 'uploads/prodotti/' . $prodotto['immagine'])) {
            unlink(FCPATH . 'uploads/prodotti/' . $prodotto['immagine']);
        }

        // 2. Cancella fisicamente tutte le foto della galleria
        foreach ($galleria as $f) {
            if (file_exists(FCPATH . 'uploads/galleria/' . $f['foto'])) {
                unlink(FCPATH . 'uploads/galleria/' . $f['foto']);
            }
        }

        // 3. Cancella i record dal DB
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
            // Cancella file fisico
            if (file_exists(FCPATH . 'uploads/galleria/' . $foto['foto'])) {
                unlink(FCPATH . 'uploads/galleria/' . $foto['foto']);
            }
            
            $prodottoId = $foto['prodotto_id'];
            $galleriaModel->delete($idFoto);
            return redirect()->to('admin/modifica/' . $prodottoId)->with('success', 'Foto eliminata');
        }

        return redirect()->back();
    }
    // Aggiungi questi metodi alla classe Admin
public function inventario()
{
    $model = new \App\Models\ProdottiModel();
    $data['prodotti'] = $model->findAll();
    return view('admin/inventario', $data);
}
// Aggiungi questo metodo alla classe Admin
public function aggiorna_stock()
{
    if (session()->get('ruolo') != 'admin') return redirect()->to('/');

    $id = $this->request->getPost('id');
    $taglia = $this->request->getPost('taglia');
    $colore = $this->request->getPost('colore');
    $nuovaQty = (int)$this->request->getPost('quantita');

    $model = new \App\Models\ProdottiModel();
    $prodotto = $model->find($id);

    if ($prodotto) {
        $magazzino = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];
        
        // Aggiorniamo solo la variante specifica
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
public function statistiche()
{
    if (session()->get('ruolo') != 'admin') return redirect()->to('/');

    $ordiniModel = new \App\Models\OrdiniModel();
    
    // Filtri GET
    $filtroGiorno = $this->request->getGet('giorno');
    $filtroMese   = $this->request->getGet('mese'); // 01, 02...
    $filtroAnno   = $this->request->getGet('anno') ?: date('Y'); // Default anno attuale

    // Query per il periodo selezionato
    $query = $ordiniModel->where('stato !=', 'Annullato');

    if (!empty($filtroGiorno)) {
        $query->where('DATE(created_at)', $filtroGiorno);
        $labelFiltro = "Giorno: " . date('d/m/Y', strtotime($filtroGiorno));
    } else {
        $query->where('YEAR(created_at)', $filtroAnno);
        if (!empty($filtroMese)) {
            $query->where('MONTH(created_at)', $filtroMese);
            $labelFiltro = "Mese: $filtroMese/$filtroAnno";
        } else {
            $labelFiltro = "Anno: $filtroAnno";
        }
    }

    $ordini = $query->findAll();

    // Calcolo Totale Anno Corrente (per il box riassuntivo)
    $totaleAnno = $ordiniModel->where('stato !=', 'Annullato')
                              ->where('YEAR(created_at)', $filtroAnno)
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
        '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
        '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
        '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
    ];

    return view('admin/statistiche', [
        'stats'        => $stats,
        'filtroGiorno' => $filtroGiorno,
        'filtroMese'   => $filtroMese,
        'filtroAnno'   => $filtroAnno,
        'mesi'         => $mesiItaliani
    ]);
}
}