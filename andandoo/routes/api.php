<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZonesController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\VoitureController;

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
//Reservation
Route::middleware('auth:apiut,client:client')->group(function () {
    Route::get('/ListReservation', [ReservationController::class, 'index']);
    Route::get('/Details/{reservation}', [ReservationController::class, 'show']);
    Route::post('/CreateReservation', [ReservationController::class, 'store']);
    Route::post('/UpdateReservation/{reservation}', [ReservationController::class, 'update']); //verbe put marche pas
    Route::delete('/DeleteReservation/{reservation}', [ReservationController::class, 'destroy']);
});

//Trajet
Route::middleware('auth:apiut,client:chauffeur')->group(function () {
    Route::get('/ListTrajet', [TrajetController::class, 'index']);
    Route::post('/CreateTrajet', [TrajetController::class, 'store']);
    Route::post('/UpdateTrajet/{trajet}', [TrajetController::class, 'update']); //verbe put marche pas
    Route::delete('/DeleteTrajet/{trajet}', [TrajetController::class, 'destroy']);
});
Route::post('/DetailsTrajet/{trajet}', [TrajetController::class, 'show']);

Route::middleware('auth:apiut,client:chauffeur')->group(function () {
    // Voiture
    Route::get('/SeeMoreVoiture', [VoitureController::class, 'index']);
    Route::post('/AjouterVoiture', [VoitureController::class, 'store']);
    Route::post('/ModifierVoiture/{voiture}', [VoitureController::class, 'update']);
    //Reservation
    Route::get('/ListReservations', [UtilisateurController::class, 'index']);
    Route::get('/DetailsReservation/{reservation}', [UtilisateurController::class, 'show']);
    Route::post('/AccepterReservation/{reservation}', [UtilisateurController::class, 'update']); //verbe put marche pas
    Route::delete('/AnnulerReservation/{reservation}', [UtilisateurController::class, 'destroy']);

    //Activer/desactiver Reservation
    Route::get('/ActiverReservation', [UtilisateurController::class, 'active']);
    Route::get('/DesactiverReservation', [UtilisateurController::class, 'inactive']); //verbe put marche pas

});
