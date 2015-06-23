<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('login', 'AuthController@getLogin');
$app->get('github',  'AuthController@getGithub');

$app->group(['middleware' => ['auth', 's3'], 'namespace' => 'App\Http\Controllers'], function($app) {
    $app->get('/', 'S3Controller@getResource');

    /**
     * Catch all route for everything else; forwards to S3 Bucket.
     * This must be the LAST route defined! You'll mess things up if you put any routes after this one.
     */
    $app->get('{path:.+}', 'S3Controller@getResource');
});
