<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\ZonesController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\NewsletterController;

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

Route::post('forget-password', [ForgetPasswordController::class, 'submitForgetPasswordForm'])
    ->name('forget.password.post');
Route::get('reset-password/{token}', [ForgetPasswordController::class, 'showResetPasswordForm'])
    ->name('reset.password.get');
Route::post('reset-password', [ForgetPasswordController::class, 'submitResetPasswordForm'])
    ->name('reset.password.post');
Route::post('/BlockerTemporairement/{user}', [AuthController::class, 'blockTemporarilyUser']);
Route::post('/BlockerDefinitivement/{user}', [AuthController::class, 'blockPermanentlyUser']);
Route::post('/Debloquer/{user}', [AuthController::class, 'unblockUser']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'loginuser']);
Route::post('/registeredadmin', [AuthController::class, 'RegisterAdmin']);
Route::post('/loginadmin', [AuthController::class, 'login']);
Route::post('/logoutadmin', [AuthController::class, 'logout']);
//Zones
Route::post('/createzone', [ZonesController::class, 'create']);
Route::post('/updatezone/{zones}', [ZonesController::class, 'update']);
Route::get('/listzone', [ZonesController::class, 'show']);
Route::delete('/deletezone/{zones}', [ZonesController::class, 'delete']);

//Reservation
Route::middleware('auth:apiut', 'role:client')->group(function () {
    Route::get('/ListReservation', [ReservationController::class, 'index']);
    Route::get('/Details/{reservation}', [ReservationController::class, 'show']);
    Route::post('/CreateReservation', [ReservationController::class, 'store']);
    Route::post('/UpdateReservation/{reservation}', [ReservationController::class, 'update']); //verbe put marche pas
    Route::delete('/DeleteReservation/{reservation}', [ReservationController::class, 'destroy']);
    Route::delete('/DeleteReservations', [ReservationController::class, 'delete']);
    Route::post('Donner/avis', [AvisController::class, 'create']);
    Route::post('Modifier/avis/{avis}', [AvisController::class, 'update']);
});

//Trajet
//oui
Route::get('/ListTrajet', [TrajetController::class, 'index']);
// Route::middleware('auth:apiut,role:chauffeur')->group(function () {
//     Route::get('/mestrajets', [TrajetController::class, 'mestrajets']);
//     Route::post('/CreateTrajet', [TrajetController::class, 'store']);
//     Route::post('/UpdateTrajet/{trajet}', [TrajetController::class, 'update']); //verbe put marche pas
//     Route::delete('/DeleteTrajet/{trajet}', [TrajetController::class, 'destroy']);
//     // Voiture
//     Route::get('/SeeMoreVoiture', [VoitureController::class, 'index']);
//     Route::post('/AjouterVoiture', [VoitureController::class, 'store']);
//     Route::post('/ModifierVoiture/{voiture}', [VoitureController::class, 'update']);
//     //Reservation
//     Route::get('/ListReservations', [UtilisateurController::class, 'index']);
//     Route::get('/DetailsReservation/{reservation}', [UtilisateurController::class, 'show']);
//     Route::post(
//         '/AccepterReservation/{reservation}',
//         [UtilisateurController::class, 'update']
//     ); //verbe put marche pas
//     Route::delete('/AnnulerReservation/{reservation}', [UtilisateurController::class, 'destroy']);
// });
Route::middleware('auth:apiut', 'role:chauffeur')->group(function () {
    Route::get('/mestrajets', [TrajetController::class, 'mestrajets']);
    Route::post('/CreateTrajet', [TrajetController::class, 'store']);
    Route::post('/UpdateTrajet/{trajet}', [TrajetController::class, 'update']);
    Route::delete('/DeleteTrajet/{trajet}', [TrajetController::class, 'destroy']);
    // Voiture
    Route::get('/SeeMoreVoiture', [VoitureController::class, 'index']);
    Route::post('/AjouterVoiture', [VoitureController::class, 'store']);
    Route::post('/ModifierVoiture/{voiture}', [VoitureController::class, 'update']);
});

Route::get('/lister/{utilisateur}', [AvisController::class, 'show']);
Route::post('/DetailsTrajet/{trajet}', [TrajetController::class, 'show']);
Route::post('/envoyer/newsletter', [NewsletterController::class, 'create']);
Route::middleware('auth:api')->group(function () {
    Route::get('/lister/newsletter', [NewsletterController::class, 'index']);
    Route::get('/listerChauffeur', [UtilisateurController::class, 'showChauffeur']);
    Route::get('/listerClient', [UtilisateurController::class, 'showClient']);
    Route::get('/listerUtilisateur', [UtilisateurController::class, 'showUsers']);
    Route::get('/listerVoiture/Disponible', [VoitureController::class, 'showVoitureD']);
    Route::get('/listerVoiture/Indisponible', [VoitureController::class, 'showVoitureInd']);
    Route::get('/listerVoitures', [VoitureController::class, 'showVoiture']);
});

// Messages Admin
Route::get('/listMessage', [MessageController::class, 'show']);
Route::post('/envoyer', [MessageController::class, 'send']);
Route::post('/repondre/Message', [MessageController::class, 'response']);
Route::middleware('auth:apiut')->group(function () {
    Route::post('/logout/user', [UtilisateurController::class, 'logout']);
    Route::post('/Update/Profile', [UtilisateurController::class, 'updateProfile']);
});
