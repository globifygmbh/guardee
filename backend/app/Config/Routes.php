<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function ($routes) {
    // Public
    $routes->post('auth/login', 'Api\AuthController::login');
    $routes->post('auth/register', 'Api\AuthController::register');

    // Protected routes
    $routes->group('', ['filter' => 'auth'], function ($routes) {
        $routes->get('auth/me', 'Api\AuthController::me');

        // Campaigns
        $routes->get('campaigns', 'Api\CampaignController::index');
        $routes->get('campaigns/pending', 'Api\CampaignController::pending');
        $routes->get('campaigns/(:num)', 'Api\CampaignController::show/$1');
        $routes->post('campaigns', 'Api\CampaignController::create');
        $routes->put('campaigns/(:num)', 'Api\CampaignController::update/$1');
        $routes->post('campaigns/(:num)/approve', 'Api\CampaignController::approve/$1');
        $routes->post('campaigns/(:num)/reject', 'Api\CampaignController::reject/$1');

        // Invitations
        $routes->get('campaigns/(:num)/invitations', 'Api\InvitationController::byCampaign/$1');
        $routes->post('invitations', 'Api\InvitationController::invite');
        $routes->post('invitations/(:num)/respond', 'Api\InvitationController::respond/$1');
        $routes->get('invitations/mine', 'Api\InvitationController::myInvitations');

        // Offers
        $routes->post('offers', 'Api\OfferController::create');
        $routes->put('offers/(:num)', 'Api\OfferController::update/$1');
        $routes->post('offers/(:num)/accept', 'Api\OfferController::accept/$1');
        $routes->post('offers/(:num)/decline', 'Api\OfferController::decline/$1');
        $routes->post('offers/(:num)/accept-terms', 'Api\OfferController::acceptTerms/$1');
        $routes->get('invitations/(:num)/offer', 'Api\OfferController::byInvitation/$1');

        // Assets
        $routes->post('assets/upload', 'Api\AssetController::upload');
        $routes->get('campaigns/(:num)/assets', 'Api\AssetController::byCampaign/$1');
        $routes->post('assets/(:num)/approve', 'Api\AssetController::approve/$1');
        $routes->post('assets/(:num)/request-revision', 'Api\AssetController::requestRevision/$1');
        $routes->get('assets/(:num)/download', 'Api\AssetController::download/$1');

        // Users
        $routes->get('users/influencers', 'Api\UserController::influencers');
        $routes->get('users/brands', 'Api\UserController::brands');
        $routes->get('users/(:num)', 'Api\UserController::show/$1');

        // Notifications
        $routes->get('notifications', 'Api\NotificationController::index');
        $routes->put('notifications/(:num)/read', 'Api\NotificationController::read/$1');
        $routes->get('notifications/unread-count', 'Api\NotificationController::unreadCount');
    });
});
