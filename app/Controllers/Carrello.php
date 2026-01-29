<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProdottiModel;
use App\Models\OrdiniModel;
use App\Models\ConfigurazioniModel; 

class Carrello extends BaseController
{
    public function index()
    {
        $carrello = session()->get('carrello') ?? [];
        return view('carrello/vendi', ['items' => $carrello]);
    }

    public function aggiungi()
    {
        if (session()->get('ruolo') == 'admin') {
            return redirect()->back()->with('errore', 'Gli amministratori non possono fare acquisti.');
        }

        $prodottiModel = new ProdottiModel();
        $idProdotto = $this->request->getPost('id');
        $variante = $this->request->getPost('variante'); 
        $qtyRichiesta = (int)$this->request->getPost('quantita');

        if (empty($variante)) {
            return redirect()->back()->with('errore', 'Devi selezionare Taglia e Colore!');
        }

        list($taglia, $colore) = explode('-', $variante);
        $prodotto = $prodottiModel->find($idProdotto);
        
        if (!$prodotto) return redirect()->back()->with('errore', 'Prodotto non trovato.');

        $magazzino = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];
        $qtyInStock = (int)($magazzino[$taglia][$colore] ?? 0);

        $rowid = $prodotto['id'] . '_' . $taglia . '_' . $colore;
        $session = session();
        $carrello = $session->get('carrello') ?? [];
        $qtyGiaInCarrello = isset($carrello[$rowid]) ? $carrello[$rowid]['quantita'] : 0;

        if (($qtyGiaInCarrello + $qtyRichiesta) > $qtyInStock) {
            return redirect()->back()->with('errore', "Disponibilità insufficiente. In magazzino: $qtyInStock");
        }

        $item = [
            'id'       => $prodotto['id'],
            'nome'     => $prodotto['nome'],
            'prezzo'   => $prodotto['prezzo'],
            'taglia'   => $taglia,
            'colore'   => $colore,
            'immagine' => $prodotto['immagine'], 
            'quantita' => $qtyGiaInCarrello + $qtyRichiesta,
            'rowid'    => $rowid 
        ];

        $carrello[$rowid] = $item;
        $session->set('carrello', $carrello);

        return redirect()->to('/carrello')->with('messaggio', "Prodotto aggiunto al carrello!");
    }

    public function aggiorna()
    {
        $session = session();
        $carrello = $session->get('carrello') ?? [];
        $rowid = $this->request->getPost('rowid');
        $nuovaQty = (int)$this->request->getPost('quantita');

        if (isset($carrello[$rowid])) {
            $prodottiModel = new ProdottiModel();
            $prodotto = $prodottiModel->find($carrello[$rowid]['id']);
            $magazzino = is_string($prodotto['magazzino']) ? json_decode($prodotto['magazzino'], true) : $prodotto['magazzino'];
            $maxDisponibile = (int)($magazzino[$carrello[$rowid]['taglia']][$carrello[$rowid]['colore']] ?? 0);

            if ($nuovaQty > $maxDisponibile) {
                return redirect()->back()->with('errore', "Solo $maxDisponibile pezzi disponibili.");
            }

            if ($nuovaQty <= 0) {
                unset($carrello[$rowid]);
            } else {
                $carrello[$rowid]['quantita'] = $nuovaQty;
            }
            $session->set('carrello', $carrello);
        }
        return redirect()->to('/carrello')->with('messaggio', 'Carrello aggiornato.');
    }

    public function rimuovi($rowid)
    {
        $session = session();
        $carrello = $session->get('carrello');
        if (isset($carrello[$rowid])) {
            unset($carrello[$rowid]);
            $session->set('carrello', $carrello);
        }
        return redirect()->to('/carrello')->with('messaggio', 'Prodotto rimosso.');
    }

    public function svuota()
    {
        session()->remove('carrello');
        return redirect()->to('/carrello');
    }

    public function checkout()
    {
        $carrello = session()->get('carrello');
        if (empty($carrello)) return redirect()->to('/carrello')->with('errore', 'Carrello vuoto.');
        return view('carrello/checkout', ['items' => $carrello]);
    }

    // =========================================================================
    // CONFERMA ORDINE (SENZA CITTÀ, SENZA EMAIL)
    // =========================================================================
    public function conferma()
    {
        $session = session();
        $carrello = $session->get('carrello');
        if (empty($carrello)) return redirect()->to('/');

        $ordiniModel  = new OrdiniModel();
        $prodottiModel= new ProdottiModel();
        $utentiModel  = new \App\Models\UtentiModel(); 
        $confModel    = new ConfigurazioniModel(); 

        $totale = 0;
        $carrelloPulito = [];

        // 1. Calcolo totale e Stock
        foreach ($carrello as $item) {
            $totale += $item['prezzo'] * $item['quantita'];
            $carrelloPulito[] = [
                'id' => $item['id'], 
                'nome' => $item['nome'], 
                'prezzo' => $item['prezzo'],
                'taglia' => $item['taglia'], 
                'colore' => $item['colore'], 
                'quantita' => $item['quantita']
            ];

            // Scala Stock
            $prod = $prodottiModel->find($item['id']);
            $stock = is_string($prod['magazzino']) ? json_decode($prod['magazzino'], true) : $prod['magazzino'];
            
            if (isset($stock[$item['taglia']][$item['colore']])) {
                $stock[$item['taglia']][$item['colore']] -= $item['quantita'];
                if ($stock[$item['taglia']][$item['colore']] < 0) $stock[$item['taglia']][$item['colore']] = 0;
            }
            $prodottiModel->update($item['id'], ['magazzino' => json_encode($stock)]);
        }

        // 2. Logica Fedeltà
        $scontoApplicato = 0;
        $puntiDaScalare = 0;
        $notaOmaggio = null;

        if (session()->get('isLoggedIn')) {
            $userId = session()->get('id');
            $user = $utentiModel->find($userId);
            $userPunti = $user['punti_fedelta'];

            // Usiamo where()->first() per evitare errori se manca l'ID
            $dataDb = $confModel->where('chiave', 'regole_fedelta')->first();
            $regole = ($dataDb) ? json_decode($dataDb['valore'], true) : [];

            usort($regole, function($a, $b) {
                return $b['punti'] - $a['punti'];
            });

            foreach ($regole as $regola) {
                if ($userPunti >= $regola['punti']) {
                    $perc = $regola['sconto'];
                    $puntiDaScalare = $regola['punti'];
                    $scontoApplicato = ($totale * $perc) / 100;
                    $totale -= $scontoApplicato; 
                    $notaOmaggio = "Sconto Fedeltà {$perc}% applicato (-€" . number_format($scontoApplicato, 2) . ")";
                    break;
                }
            }
        }

        // 3. Prepariamo i dati dell'ordine (SENZA CITTÀ)
        $datiOrdine = [
            'nome_cliente'      => $this->request->getPost('nome_cliente'),
            'email'             => $this->request->getPost('email'),
            'indirizzo'         => $this->request->getPost('indirizzo'),
            'totale'            => $totale, 
            'dettagli_prodotti' => json_encode($carrelloPulito),
            'stato'             => 'In lavorazione',
            'created_at'        => date('Y-m-d H:i:s'),
            'omaggi'            => $notaOmaggio
        ];

        // 4. Salvataggio
        if ($ordiniModel->insert($datiOrdine)) {
            $orderId = $ordiniModel->getInsertID();

            // 5. Aggiornamento Punti
            if (session()->get('isLoggedIn')) {
                $userAggiornato = $utentiModel->find($userId);
                $puntiAttuali = $userAggiornato['punti_fedelta'];
                $puntiAttuali -= $puntiDaScalare;
                $puntiGuadagnati = (int) $totale; 
                $saldoFinale = $puntiAttuali + $puntiGuadagnati;
                $utentiModel->update($userId, ['punti_fedelta' => $saldoFinale]);
                session()->set('punti_fedelta', $saldoFinale);
            }

            // 6. Pulizia e Successo (NIENTE EMAIL)
            $session->remove('carrello');
            return view('carrello/successo', ['order_id' => $orderId]);
        }

        return redirect()->back()->with('errore', 'Errore durante il salvataggio dell\'ordine.');
    }
}