<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public
$routes->get('/', 'AuthController::loginForm');
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('register', 'AuthController::registerForm');
$routes->post('register', 'AuthController::register');
$routes->get('logout', 'AuthController::logout');

// Protected
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Campaigns
    $routes->get('campaigns', 'CampaignController::index');
    $routes->get('campaigns/create', 'CampaignController::create');
    $routes->post('campaigns/store', 'CampaignController::store');
    $routes->get('campaigns/pending', 'CampaignController::pending');
    $routes->get('campaigns/(:num)', 'CampaignController::show/$1');
    $routes->post('campaigns/(:num)/update', 'CampaignController::update/$1');
    $routes->post('campaigns/(:num)/approve', 'CampaignController::approve/$1');
    $routes->post('campaigns/(:num)/reject', 'CampaignController::reject/$1');

    // Invitations
    $routes->get('invitations', 'InvitationController::index');
    $routes->post('invitations/invite', 'InvitationController::invite');
    $routes->post('invitations/(:num)/respond', 'InvitationController::respond/$1');

    // Offers
    $routes->get('offers/(:num)', 'OfferController::show/$1');
    $routes->post('offers/create', 'OfferController::create');
    $routes->post('offers/(:num)/update', 'OfferController::update/$1');
    $routes->post('offers/(:num)/accept', 'OfferController::accept/$1');
    $routes->post('offers/(:num)/decline', 'OfferController::decline/$1');
    $routes->post('offers/(:num)/accept-terms', 'OfferController::acceptTerms/$1');

    // Assets
    $routes->post('assets/upload', 'AssetController::upload');
    $routes->post('assets/(:num)/approve', 'AssetController::approve/$1');
    $routes->post('assets/(:num)/request-revision', 'AssetController::requestRevision/$1');
    $routes->get('assets/(:num)/download', 'AssetController::download/$1');

    // Users (admin)
    $routes->get('users', 'UserController::index');
    $routes->get('users/influencers', 'UserController::influencers');

    // Notifications
    $routes->get('notifications', 'NotificationController::index');
    $routes->post('notifications/(:num)/read', 'NotificationController::markRead/$1');
});
