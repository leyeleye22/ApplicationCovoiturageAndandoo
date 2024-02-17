<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Trajet;
use App\Models\Utilisateur;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;
use App\Events\ActivationReservation;
use Illuminate\Support\Facades\Cache;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\StoreTrajetRequest;
use App\Http\Requests\UpdateTrajetRequest;

class TrajetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {


            $trajets = Cache::rememberForever('trajets', function () {
                return Trajet::all();
            });

            $data = [];


            foreach ($trajets as $trajet) {

                $totalPlaces = $trajet->voiture->NbrPlaces;
                $totalPlaceReserve = $trajet->reservations()->where('Accepted', true)->sum('NombrePlaces');
                $placeDispo = $totalPlaces - $totalPlaceReserve;
                $chauffeur = $trajet->voiture->utilisateur;
                $nom = $chauffeur->Nom;
                $prenom = $chauffeur->Prenom;
                $imageChauffeur = $chauffeur->ImageProfile;
                $imageVoiture = $trajet->voiture->ImageVoitures;

                $data[] = [
                    'id' => $trajet['id'],
                    'LieuDepart' => $trajet['LieuDepart'],
                    'LieuArrivee' => $trajet['LieuArrivee'],
                    'DateDepart' => $trajet['DateDepart'],
                    'HeureDepart' => $trajet['HeureD'],
                    'Prix' => $trajet['Prix'],
                    'Description' => $trajet['DescriptionTrajet'],
                    'NombrePlaceDisponible' => $placeDispo,
                    'Status' => $trajet['Status'],
                    'ChauffeurId' => $chauffeur->id,
                    'NomChauffeur' => $nom,
                    'PrenomChauffeur' => $prenom,
                    'ImageProfile' => $imageChauffeur,
                    'ImageVoiture' => $imageVoiture,
                ];
            }


            if (!empty($data)) {
                return response()->json($data);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Aucun trajet trouvé.',
                ], 404);
            }
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la récupération des trajets.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function mestrajets()
    {
        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            $userId = Auth::guard('apiut')->user()->id;
            $userCarId = Auth::guard('apiut')->user()->voiture->id;
            $mestrajets = Cache::rememberForever('trajets_' . $userId, function () use ($userCarId) {
                return Trajet::where('voiture_id', $userCarId)->where('Status', 'enCours')->get();
            });

            foreach ($mestrajets as $mestrajet) {

                $totalPlaces = $mestrajet->voiture->NbrPlaces;
                $totalPlaceReserve = $mestrajet->reservations()->where('Accepted', true)->sum('NombrePlaces');
                $placeDispo = $totalPlaces - $totalPlaceReserve;


                $chauffeur = $mestrajet->voiture->utilisateur;
                $nom = $chauffeur->Nom;
                $prenom = $chauffeur->Prenom;
                $imageChauffeur = $chauffeur->ImageProfile;
                $imageVoiture = $mestrajet->voiture->ImageVoitures;
                $data[] = [
                    'id' => $mestrajet['id'],
                    'LieuDepart' => $mestrajet['LieuDepart'],
                    'LieuArrivee' => $mestrajet['LieuArrivee'],
                    'DateDepart' => $mestrajet['DateDepart'],
                    'HeureDepart' => $mestrajet['HeureD'],
                    'Prix' => $mestrajet['Prix'],
                    'Description' => $mestrajet['DescriptionTrajet'],
                    'NombrePlaceDisponible' => $placeDispo,
                    'Status' => $mestrajet['Status'],
                    'ChauffeurId' => $chauffeur->id,
                    'NomChauffeur' => $nom,
                    'PrenomChauffeur' => $prenom,
                    'ImageProfile' => $imageChauffeur,
                    'ImageVoiture' => $imageVoiture,
                ];
            }
            if ($mestrajets) {
                return response()->json($data);
            } else {
                return response()->json([
                    'messages' => 'Vous avez 0 trajet'
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrajetRequest $request)
    {
        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            $validatedData = $request->validated();
            $trajet = new Trajet();
            $carId = Auth::guard('apiut')->user()->voiture->id;
            $cacheKey = 'trajet_' . $carId . '_' . $validatedData['DateDepart'] . '_' . $validatedData['HeureD'];
            $dateexist = Cache::remember($cacheKey, 3600, function () use ($carId, $validatedData) {
                return Trajet::where('DateDepart', $validatedData['DateDepart'])
                    ->where('HeureD', $validatedData['HeureD'])
                    ->where('voiture_id', $carId)
                    ->first();
            });
            if ($dateexist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez deja un trajet a cette heure',
                ]);
            }
            $trajet->fill($validatedData);
            $trajet->voiture_id = $carId;
            $trajet->DescriptionTrajet = $request->DescriptionTrajet;
            if ($trajet->save()) {
                Artisan::call('optimize:clear');
                event(new ActivationReservation($trajet));
                $users = Utilisateur::all();
                foreach ($users as $user) {
                    $user->notify(new UserNotification($trajet));
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Trajet enregistré avec succès.',
                    'data' => $trajet,
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'enregistrement du trajet',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'enregistrement du trajet.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Trajet $trajet)
    {
        try {
            if ($trajet) {

                return response()->json([
                    'success' => true,
                    'message' => 'Trajet reccupéré avec succès.',
                    'data' => $trajet
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec du recuperation du trajet',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du recuperation du trajet.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrajetRequest $request, Trajet $trajet)
    {
        $success = false;
        $message = '';
        $data = null;
        $statusCode = 500;

        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            $validatedData = $request->validated();
            if ($trajet->voiture_id !== Auth::guard('apiut')->user()->voiture->id) {
                throw new Exception('Vous n\'êtes pas autorisé à modifier ce trajet.');
            }

            if ($trajet->update($validatedData)) {
                Artisan::call('optimize:clear');
                $success = true;
                $message = 'Trajet modifié avec succès.';
                $data = $trajet;
                $statusCode = 200;
            } else {
                $message = 'Échec de la modification du trajet.';
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $statusCode = 403;
        }

        return response()->json(['success' => $success, 'message' => $message, 'data' => $data], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trajet $trajet)
    {

        $success = false;
        $message = '';
        $statusCode = 500;

        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            if ($trajet->voiture_id !== Auth::guard('apiut')->user()->voiture->id) {
                throw new Exception('Vous n\'êtes pas autorisé à supprimer ce trajet.');
            }

            if ($trajet->delete()) {
                Artisan::call('optimize:clear');
                $success = true;
                $message = 'Trajet supprimé avec succès.';
                $statusCode = 200;
            } else {
                $message = 'Échec de la suppression du trajet.';
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $statusCode = 403;
        }

        return response()->json(['success' => $success, 'message' => $message], $statusCode);
    }
}
