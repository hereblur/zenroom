<?php

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

$app->get('/', function () use ($app) {
  return $app->version();
});

$app->group(["prefix"=>"/api"], function() use ($app) {
  $app->get('/roomtypes', "RoomtypesController@index");
  $app->put('/roomtypes', "RoomtypesController@create");
  $app->post('/roomtypes/{id}', "RoomtypesController@update");

  $app->get('/bookings/{month}', "BookingsController@index");
  $app->post('/bookings/{date}/{roomtype}', "BookingsController@store");
  $app->post('/bookings/bulk', "BookingsController@bulk");
});
