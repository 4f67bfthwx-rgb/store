<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// 1. LATO PUBBLICO
// =========================================================================
$routes->get('/', 'Home::index');
$routes->get('prodotto/(:num)', 'Home::dettaglio/$1');

// =========================================================================
// 2. AUTENTICAZIONE
// =========================================================================
$routes->get('login', 'Auth::login');
$routes->post('loginAuth', 'Auth::loginAuth');
$routes->get('register', 'Auth::register');
$routes->post('store', 'Auth::store');
$routes->get('logout', 'Auth::logout');

// *** QUESTA È LA RIGA CHE MANCAVA ***
$routes->get('profilo', 'Profile::index');

// =========================================================================
// 3. CARRELLO E CHECKOUT
// =========================================================================
$routes->get('carrello', 'Carrello::index');
$routes->post('carrello/aggiungi', 'Carrello::aggiungi');
$routes->post('carrello/aggiorna', 'Carrello::aggiorna');
$routes->get('carrello/rimuovi/(:any)', 'Carrello::rimuovi/$1');
$routes->get('carrello/svuota', 'Carrello::svuota');
$routes->get('carrello/checkout', 'Carrello::checkout');
$routes->post('carrello/conferma', 'Carrello::conferma');
$routes->post('checkout/conferma', 'Carrello::conferma');

// =========================================================================
// 4. LATO ADMIN (Protetto dal prefisso "admin")
// =========================================================================
$routes->group('admin', function($routes) {
    
    // Dashboard principale
    $routes->get('dashboard', 'Admin::dashboard'); 

    // GESTIONE PRODOTTI
    $routes->get('prodotti', 'Admin::index'); 
    $routes->get('crea', 'Admin::crea');
    $routes->post('salva', 'Admin::salva');
    $routes->get('modifica/(:num)', 'Admin::modifica/$1');
    $routes->post('aggiorna/(:num)', 'Admin::aggiorna/$1');
    $routes->get('elimina/(:num)', 'Admin::elimina/$1');
    $routes->get('elimina_foto/(:num)', 'Admin::eliminaFoto/$1');

    // CONFIGURAZIONI FEDELTÀ
    $routes->post('aggiungiRegolaFedelta', 'Admin::aggiungiRegolaFedelta');
    $routes->get('rimuoviRegolaFedelta/(:num)', 'Admin::rimuoviRegolaFedelta/$1');

    // OMAGGI
    $routes->post('aggiungiOmaggio', 'Admin::aggiungiOmaggio');

    // INVENTARIO E STATISTICHE
    $routes->get('inventario', 'Admin::inventario');
    $routes->get('statistiche', 'Admin::statistiche');
    $routes->post('aggiorna_stock', 'Admin::aggiorna_stock');
    
    // CONFIGURAZIONI NEGOZIO
    $routes->post('salvaConfigurazione', 'Admin::salvaConfigurazione');

    // ORDINI
    $routes->get('ordini', 'AdminOrdini::index');
    $routes->get('cambiaStato/(:num)/(:any)', 'Admin::cambiaStato/$1/$2');

    // STAFF
    $routes->get('nuovo-admin', 'AdminUtenti::nuovo');
    $routes->post('salva-admin', 'AdminUtenti::crea');
});