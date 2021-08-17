<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

function AuthResource($router,$uri,$controller){
    //$verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];
    $router->post($uri.'/signup', $controller.'@signUp');
    $router->post($uri.'/activate', $controller.'@activate');
    $router->post($uri.'/signin', $controller.'@signIn');
    $router->post($uri.'/forgetPassword', $controller.'@forgetPassword');
    $router->post($uri.'/resetPassword', $controller.'@resetPassword');
    $router->post($uri.'/signout', $controller.'@signOut');
}

function TwitResource($router,$uri,$controller){
    //$verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];
    $router->post($uri.'/', $controller.'@create');
    $router->get($uri.'/', $controller.'@read');
    $router->get($uri.'/{id}', $controller.'@readOne');
    //$router->put($uri.'/{id}', $controller.'@update');
    $router->delete($uri.'/{id}', $controller.'@delete');
    $router->get($uri.'/user/profile', $controller.'@readByUser');
}

function CommentResource($router,$uri,$controller){
    $router->post($uri.'/', $controller.'@create');
}

function LikeResource($router,$uri,$controller){
    $router->post($uri.'/', $controller.'@create');
}

$router->group(['prefix'=>'api/'], function() use($router){
    AuthResource($router,'auth','AuthController');
    TwitResource($router,'twit','TwitController');
    CommentResource($router,'comment','CommentController');
    LikeResource($router,'like','LikesController');
});
