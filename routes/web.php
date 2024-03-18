<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

$router->get('/', function () {
    return 'Api Fiel';
});
$router->get('/info', ['middleware' => 'auth', 'uses' => 'FielXmlController@info']);
$router->get('/authorization', ['middleware' => 'auth', 'uses' => 'FielXmlController@authorization']);
$router->post('/query',  ['middleware' => 'auth', 'uses' => 'FielXmlController@query']);
$router->get('/verify/{requestId}', ['middleware' => 'auth', 'uses' =>  'FielXmlController@verify']);
$router->get('/download/{packageId}', ['middleware' => 'auth', 'uses' =>  'FielXmlController@download']);
