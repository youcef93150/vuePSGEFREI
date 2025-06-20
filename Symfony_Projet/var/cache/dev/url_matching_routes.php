<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/register' => [[['_route' => 'api_register', '_controller' => 'App\\Controller\\ApiController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'api_login', '_controller' => 'App\\Controller\\LoginController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/check-auth' => [[['_route' => 'api_check_auth', '_controller' => 'App\\Controller\\AuthController::checkAuth'], null, ['GET' => 0], null, false, false, null]],
        '/api/joueurs' => [
            [['_route' => 'get_joueurs', '_controller' => 'App\\Controller\\JoueurController::getJoueurs'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'add_joueur', '_controller' => 'App\\Controller\\JoueurController::addJoueur'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/produits' => [
            [['_route' => 'get_products', '_controller' => 'App\\Controller\\BoutiqueAdminController::getProducts'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'add_product', '_controller' => 'App\\Controller\\BoutiqueAdminController::addProduct'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'boutique_add_product', '_controller' => 'App\\Controller\\BoutiqueAdminController::addProduct'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'boutique_get_products', '_controller' => 'App\\Controller\\BoutiqueAdminController::getProducts'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/dashboard/stats' => [[['_route' => 'dashboard_stats', '_controller' => 'App\\Controller\\JoueurController::getDashboardStats'], null, ['GET' => 0], null, false, false, null]],
        '/api/dashboard/joueurs-stats' => [[['_route' => 'dashboard_joueurs_stats', '_controller' => 'App\\Controller\\JoueurController::getJoueursStats'], null, ['GET' => 0], null, false, false, null]],
        '/api/dashboard/boutique-stats' => [[['_route' => 'dashboard_boutique_stats', '_controller' => 'App\\Controller\\JoueurController::getBoutiqueStats'], null, ['GET' => 0], null, false, false, null]],
        '/api/actualites' => [
            [['_route' => 'get_actualites', '_controller' => 'App\\Controller\\JoueurController::getActualites'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'add_actualite', '_controller' => 'App\\Controller\\JoueurController::addActualite'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/entrainements' => [
            [['_route' => 'get_entrainements', '_controller' => 'App\\Controller\\JoueurController::getEntrainements'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'add_entrainement', '_controller' => 'App\\Controller\\JoueurController::addEntrainement'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'api_entrainements_list', '_controller' => 'App\\Controller\\JoueurController::getEntrainements'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_entrainements_add', '_controller' => 'App\\Controller\\JoueurController::addEntrainement'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/entrainements/assigner-joueur' => [[['_route' => 'assigner_joueur_entrainement', '_controller' => 'App\\Controller\\JoueurController::assignerJoueurEntrainement'], null, ['POST' => 0], null, false, false, null]],
        '/api/entrainements/retirer-joueur' => [[['_route' => 'retirer_joueur_entrainement', '_controller' => 'App\\Controller\\JoueurController::retirerJoueurEntrainement'], null, ['DELETE' => 0], null, false, false, null]],
        '/api/matchs' => [
            [['_route' => 'api_matchs_list', '_controller' => 'App\\Controller\\JoueurController::getMatchs'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_matchs_add', '_controller' => 'App\\Controller\\JoueurController::addMatch'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/matchs/stats' => [[['_route' => 'api_matchs_stats', '_controller' => 'App\\Controller\\JoueurController::getMatchsStats'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api(?'
                    .'|/(?'
                        .'|docs(?:\\.([^/]++))?(*:37)'
                        .'|\\.well\\-known/genid/([^/]++)(*:72)'
                        .'|validation_errors/([^/]++)(*:105)'
                    .')'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:142)'
                    .'|/(?'
                        .'|contexts/([^.]+)(?:\\.(jsonld))?(*:185)'
                        .'|e(?'
                            .'|rrors/(\\d+)(?:\\.([^/]++))?(*:223)'
                            .'|ntrainements/(?'
                                .'|(\\d+)(?'
                                    .'|(*:255)'
                                .')'
                                .'|([^/]++)(*:272)'
                            .')'
                        .')'
                        .'|validation_errors/([^/]++)(?'
                            .'|(*:311)'
                        .')'
                        .'|joueurs/([^/]++)(?'
                            .'|(*:339)'
                        .')'
                        .'|produits/(?'
                            .'|([^/]++)(*:368)'
                            .'|(\\d+)/stock(?'
                                .'|(*:390)'
                            .')'
                        .')'
                        .'|actualites/(\\d+)(?'
                            .'|(*:419)'
                        .')'
                        .'|matchs/(\\d+)(?'
                            .'|(*:443)'
                        .')'
                    .')'
                .')'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:482)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        37 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        72 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => 'true'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        105 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        142 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        185 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        223 => [[['_route' => '_api_errors', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\State\\ApiResource\\Error', '_api_operation_name' => '_api_errors', '_format' => null], ['status', '_format'], ['GET' => 0], null, false, true, null]],
        255 => [
            [['_route' => 'update_entrainement', '_controller' => 'App\\Controller\\JoueurController::updateEntrainement'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'delete_entrainement', '_controller' => 'App\\Controller\\JoueurController::deleteEntrainement'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        272 => [[['_route' => 'api_entrainements_delete', '_controller' => 'App\\Controller\\JoueurController::deleteEntrainement'], ['id'], ['DELETE' => 0], null, false, true, null]],
        311 => [
            [['_route' => '_api_validation_errors_problem', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_problem', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_hydra', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_hydra', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_jsonapi', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_jsonapi', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
        ],
        339 => [
            [['_route' => 'update_joueur', '_controller' => 'App\\Controller\\JoueurController::updateJoueur'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'delete_joueur', '_controller' => 'App\\Controller\\JoueurController::deleteJoueur'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        368 => [[['_route' => 'boutique_delete_product', '_controller' => 'App\\Controller\\BoutiqueAdminController::deleteProduct'], ['id'], ['DELETE' => 0], null, false, true, null]],
        390 => [
            [['_route' => 'boutique_update_stock', '_controller' => 'App\\Controller\\BoutiqueAdminController::updateStock'], ['id'], ['PUT' => 0], null, false, false, null],
            [['_route' => 'update_stock', '_controller' => 'App\\Controller\\BoutiqueAdminController::updateStock'], ['id'], ['PUT' => 0], null, false, false, null],
        ],
        419 => [
            [['_route' => 'update_actualite', '_controller' => 'App\\Controller\\JoueurController::updateActualite'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'delete_actualite', '_controller' => 'App\\Controller\\JoueurController::deleteActualite'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        443 => [
            [['_route' => 'api_matchs_update', '_controller' => 'App\\Controller\\JoueurController::updateMatch'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'api_matchs_delete', '_controller' => 'App\\Controller\\JoueurController::deleteMatch'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        482 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
