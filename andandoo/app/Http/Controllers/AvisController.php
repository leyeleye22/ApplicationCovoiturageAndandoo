<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Trajet;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreAvisRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\UpdateAvisRequest;
use Illuminate\Validation\ValidationException;

class AvisController extends Controller
{

    public function create(StoreAvisRequest $req, Trajet $trajet)
    {
        $success = false;
        $responseData = [];
        $statusCode = 500;

        try {
            if (Auth::guard('apiut')->user()->role == "chaufeur") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            $validatedData = $req->validated();
            $avis = new Avis();
            $avis->fill($validatedData);
            $avis->utilisateur_id = Auth::guard('apiut')->user()->id;
            $avis->voiture_id = $trajet->voiture->id;
            $avis->save();
            Artisan::call('optimize:clear');
            $success = true;
            $responseData = ['success' => true, 'avis' => $avis];
            $statusCode = 200;
        } catch (ValidationException $e) {
            $responseData = ['success' => false, 'errors' => $e->errors()];
            $statusCode = 422;
        } catch (QueryException $e) {
            $responseData = ['success' => false, 'error' => 'Erreur de base de données.'];
            $statusCode = 500;
        } catch (\Exception $e) {
            $responseData = ['success' => false, 'error' => $e->getMessage()];
            $statusCode = 500;
        }

        return response()->json($responseData, $statusCode);
    }



    /**
     * list the all resource in storage.
     */
    public function lister()
    {
        $success = false;
        $responseData = [];
        $avis = 0;
        $statusCode = 500;

        try {
            $users = Cache::remember('chauffeur', 3600, function () {
                return Utilisateur::where('role', 'chauffeur')->get();
            });
            $data = [];
            foreach ($users as $user) {
                $userId = $user->id;
                $avis = Cache::remember('avis_' . $userId, 3600, function () use ($user) {
                    return Avis::where('voiture_id', $user->voiture->id)->get();
                });
                $data[] = [
                    'NomChauffeur' => $user->Nom,
                    'PrenomChauffeur' => $user->Prenom,
                    'PhotoProfile' => $user->ImageProfile,
                    'Avis' => $avis
                ];
            }

            $success = true;
            $avis = $data;
            $responseData = ['success' => true, 'message' => 'Avis ont été recuperer avec succès.'];
            $statusCode = 200;
        } catch (ValidationException $e) {
            $responseData = ['success' => false, 'errors' => $e->errors()];
            $statusCode = 422;
        } catch (QueryException $e) {
            $responseData = ['success' => false, 'error' => 'Erreur de base de données lors de la mise à jour.'];
            $statusCode = 500;
        } catch (\Exception $e) {
            $responseData = ['success' => false, 'error' => $e->getMessage()];
            $statusCode = 403;
        }

        return response()->json($avis);
    }
}
