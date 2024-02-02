<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Trajet;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;
use App\Events\ActivationReservation;
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

            $trajets = Trajet::with('voiture.utilisateur')->get();


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
            $mestrajets = Trajet::where('voiture_id', Auth::guard('apiut')->user()->voiture->id);
            $data = [];
            foreach ($mestrajets as $mestajet) {
                $data[] = [
                    'id' => $mestajet['id'],
                    'LieuDepart' => $mestajet['LieuDepart'],
                    'LieuArrivee' => $mestajet['LieuArrivee'],
                    'HeureD' => $mestajet['HeureD'],
                    'Prix' => $mestajet['Prix'],
                    'DescriptionTrajet' => $mestajet['DescriptionTrajet']
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

            $validatedData = $request->validated();
            $trajet = new Trajet();
            $id = Auth::guard('apiut')->user()->voiture->id;
            $dateexist = Trajet::where('DateDepart', $request)
                ->where('HeureD', $request->HeureD)
                ->where('voiture_id', $id)->first();
            if ($dateexist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez deja un trajet a cette heure',
                ]);
            }
            $trajet->fill($validatedData);
            $trajet->voiture_id = $id;
            $trajet->DescriptionTrajet = $request->DescriptionTrajet;
            if ($trajet->save()) {
                event(new ActivationReservation($trajet));
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
            $validatedData = $request->validated();
            if ($trajet->voiture_id !== Auth::guard('apiut')->user()->voiture->id) {
                throw new Exception('Vous n\'êtes pas autorisé à modifier ce trajet.');
            }

            if ($trajet->update($validatedData)) {
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
            if ($trajet->voiture_id !== Auth::guard('apiut')->user()->voiture->id) {
                throw new Exception('Vous n\'êtes pas autorisé à supprimer ce trajet.');
            }

            if ($trajet->delete()) {
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
