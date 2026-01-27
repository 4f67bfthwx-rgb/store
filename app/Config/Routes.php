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
$routes->post('checkout/conferma', 'Carrello::conferma'); // Alias per sicurezza

// =========================================================================
// 4. LATO ADMIN (Protetto dal prefisso "admin")
// =========================================================================
$routes->group('admin', function($routes) {
    
    // Dashboard principale
    $routes->get('dashboard', 'Admin::dashboard'); 

    // GESTIONE PRODOTTI (Lista, Crea, Modifica, Elimina)
    $routes->get('prodotti', 'Admin::index'); 
    $routes->get('crea', 'Admin::crea');
    $routes->post('salva', 'Admin::salva');
    $routes->get('modifica/(:num)', 'Admin::modifica/$1');
    $routes->post('aggiorna/(:num)', 'Admin::aggiorna/$1');
    $routes->get('elimina/(:num)', 'Admin::elimina/$1');
    $routes->get('elimina_foto/(:num)', 'Admin::eliminaFoto/$1');

    // INVENTARIO E STATISTICHE (Rimosso il prefisso admin/ interno)
    $routes->get('inventario', 'Admin::inventario');   // URL: admin/inventario
    $routes->get('statistiche', 'Admin::statistiche'); // URL: admin/statistiche
    $routes->post('aggiorna_stock', 'Admin::aggiorna_stock'); // <--- AGGIUNGI QUESTA

    // ORDINI
    $routes->get('ordini', 'AdminOrdini::index');
    $routes->get('cambia_stato/(:num)/(:any)', 'AdminOrdini::cambia_stato/$1/$2');

    // STAFF (Gestione Utenti Admin)
    $routes->get('nuovo-admin', 'AdminUtenti::nuovo');
    $routes->post('salva-admin', 'AdminUtenti::crea');
});