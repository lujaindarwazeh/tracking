<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\WayController;
use App\Http\Controllers\companycontroller;
use App\Http\Controllers\companyuserscontroller;
use App\Http\Controllers\zonecontroller;
use App\Http\Controllers\eventcontroller;

Route::get('/test', function () {
    return 'API routes are working!';
});

Route::patch('/updatecompanyuserspeed/{id}', [companyuserscontroller::class, 'updateSpeed']);
Route::patch('/updateuserlocation/{id}', [companyuserscontroller::class, 'updateLocation']);

Route::apiResource('device', DeviceController::class);
Route::apiResource('vehicle', VehicleController::class);
Route::apiResource('provider', ProviderController::class);



Route::post('/ways', [WayController::class, 'store']);
Route::post('/ways/{way}/points', [WayController::class, 'addPoints']);
Route::post('/ways/{way}/generate-polygon', [WayController::class, 'generatePolygon']);
Route::get('/ways/{way}/getgeneratePolygon', [WayController::class, 'getgeneratePolygon']);
Route::get('/ways/{way}/getgeneratePolygongeometry', [WayController::class, 'getgeneratePolygongeometry']);


Route::apiResource('company', companycontroller::class);
Route::apiResource('companyusers', companyuserscontroller::class);
Route::apiResource('zones', zonecontroller::class);
Route::apiResource('event', eventcontroller::class);
Route::post('/add_event_to_company', [eventcontroller::class, 'addEventToCompany']);





