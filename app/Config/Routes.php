<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'ClientAuth::index');

$routes->group('/operateur', static function ($routes) {
    $routes->get('/', 'OperateurController::index');
    $routes->get('creer', 'OperateurController::creerOperateur');
    $routes->post('creer', 'OperateurController::storeOperateur');
    $routes->get('situation-gains', 'OperateurController::situationGains');
    $routes->get('situation-clients', 'OperateurController::situationClients');
    $routes->get('situation-clients/(:num)', 'OperateurController::situationClientDetail/$1');
    $routes->get('promotion', 'OperateurController::promotion');
    $routes->post('promotion', 'OperateurController::promotionUpdate');
});
$routes->get('solde', 'Solde::index');

$routes->group('client', static function ($routes) {
    $routes->get('login', 'ClientAuth::index');
    $routes->post('login', 'ClientAuth::login');
    $routes->get('logout', 'ClientAuth::logout');

    $routes->group('', ['filter' => 'clientauth'], static function ($routes) {
        $routes->get('/', 'Client::index');
        $routes->get('depot', 'Client::depot');
        $routes->post('depot', 'Client::depotStore');
        $routes->get('retrait', 'Client::retrait');
        $routes->post('retrait', 'Client::retraitStore');
        $routes->get('transfert', 'Client::transfert');
        $routes->post('transfert', 'Client::transfertStore');
        $routes->get('envoi-multiple', 'Client::envoiMultiple');
        $routes->post('envoi-multiple', 'Client::envoiMultipleStore');
        $routes->get('historique', 'Client::historique');
        $routes->get('epargne', 'Client::epargne');
        $routes->post('epargne', 'Client::epargne');
    });
});

// $routes->get('/', 'Auth::index');
// $routes->get('/login', 'Auth::index');
// $routes->post('/login', 'Auth::login');
// $routes->get('/register', 'Auth::register');
// $routes->post('/register', 'Auth::store');
// $routes->get('/logout', 'Auth::logout');

// $routes->group('/employe', ['filter' => 'auth:employe'], static function ($routes) {
//     $routes->get('/', 'Employe::index');
//     $routes->get('demande', 'Employe::demande');
//     $routes->post('demande', 'Employe::store');
//     $routes->get('mes-demandes', 'Employe::mesDemandes');
//     $routes->post('annuler/(:num)', 'Employe::annuler/$1');
//     $routes->get('solde', 'Employe::solde');
//     $routes->get('profil', 'Employe::profil');
//     $routes->post('profil', 'Employe::profilUpdate');
//     $routes->get('calendrier', 'Employe::calendrier');
// });

// $routes->group('/rh', ['filter' => 'auth:rh'], static function ($routes) {
//     $routes->get('/', 'Rh::index');
//     $routes->get('demandes', 'Rh::demandes');
//     $routes->get('demandes/(:num)', 'Rh::detail/$1');
//     $routes->post('traiter/(:num)', 'Rh::traiter/$1');
//     $routes->get('soldes', 'Rh::soldes');
// });

// $routes->group('/admin', ['filter' => 'auth:admin'], static function ($routes) {
//     $routes->get('/', 'Admin::index');
//     $routes->get('employes', 'Admin::employes');
//     $routes->get('employes/creer', 'Admin::employeCreer');
//     $routes->post('employes/creer', 'Admin::employeStore');
//     $routes->get('employes/(:num)', 'Admin::employeEditer/$1');
//     $routes->post('employes/(:num)', 'Admin::employeUpdate/$1');
//     $routes->post('employes/toggle/(:num)', 'Admin::employeToggle/$1');
//     $routes->get('departements', 'Admin::departements');
//     $routes->get('departements/creer', 'Admin::departementCreer');
//     $routes->post('departements/creer', 'Admin::departementStore');
//     $routes->get('departements/(:num)', 'Admin::departementEditer/$1');
//     $routes->post('departements/(:num)', 'Admin::departementUpdate/$1');
//     $routes->post('departements/supprimer/(:num)', 'Admin::departementSupprimer/$1');
//     $routes->get('types-conge', 'Admin::typesConge');
//     $routes->get('types-conge/creer', 'Admin::typeCongeCreer');
//     $routes->post('types-conge/creer', 'Admin::typeCongeStore');
//     $routes->get('types-conge/(:num)', 'Admin::typeCongeEditer/$1');
//     $routes->post('types-conge/(:num)', 'Admin::typeCongeUpdate/$1');
//     $routes->post('types-conge/supprimer/(:num)', 'Admin::typeCongeSupprimer/$1');
//     $routes->get('demandes', 'Admin::demandes');
//     $routes->get('soldes', 'Admin::soldes');
//     $routes->get('soldes/creer', 'Admin::soldesCreer');
//     $routes->post('soldes/creer', 'Admin::soldesStore');
//     $routes->get('soldes/(:num)', 'Admin::soldesEditer/$1');
//     $routes->post('soldes/(:num)', 'Admin::soldesUpdate/$1');
//     $routes->get('historique', 'Admin::historique');
// });
