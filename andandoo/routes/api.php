<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZonesController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'loginuser']);
Route::post('/registeredadmin', [AuthController::class, 'RegisterAdmin']);
Route::post('/loginadmin', [AuthController::class, 'login']);
//Zones
Route::post('/createzone', [ZonesController::class, 'create']);
Route::post('/updatezone/{zones}', [ZonesController::class, 'update']);
Route::get('/listzone', [ZonesController::class, 'show']);
Route::delete('/deletezone/{zones}', [ZonesController::class, 'delete']);
// Messages Admin
Route::get('/listMessage', [MessageController::class, 'show']);
//Trajet
Route::middleware('auth:apiut,client:client')->group(function () {
    Route::get('/test', function () {
        return 'sa matrche';
    });
});
Route::middleware('auth:apiut,client:chauffeur')->group(function () {
    Route::get('/tests', function () {
        return 'sa matrche';
    });
});
