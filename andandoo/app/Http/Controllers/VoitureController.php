<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVoitureRequest;
use App\Http\Requests\UpdateVoitureRequest;

class VoitureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::guard('apiut')->user();
            $vehicule = $user->voiture;
            if ($vehicule) {
                return response()->json([
                    'success' => true,
                    'message' => 'Voiture récuppérré avec succès.',
                    'data' => $vehicule,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune voiture trouvé',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la récuperation de votre voiture.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVoitureRequest $request)
    {
        try {
            $user = Auth::guard('apiut')->user();
            $voiture = $user->voiture;
            if (!isset($voiture)) {
                $validatedData = $request->validated();
                $voiture = new Voiture();
                $voiture->fill($validatedData);
                $voiture->utilisateur_id = Auth::guard('apiut')->user()->id;
                if ($voiture->save()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Votre voiture a été  enregistré avec succès.',
                        'data' => $voiture,
                    ]);
                } else {
                    // La sauvegarde a échoué
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous avez déja ajouté une voiture',
                    ], 500);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déja ajouté une voiture',
                  
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'enregistrement de votre voiture.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdateVoitureRequest $request, Voiture $voiture)
    {

        try {

            if (Auth::guard('apiut')->user()->id == $voiture->utilisateur_id) {
                $validatedData = $request->validated();
                $voiture->fill($validatedData);
                if ($voiture->save()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Votre voiture a été  modifié avec succès.',
                        'data' => $voiture,
                    ]);
                } else {
                    // La sauvegarde a échoué
                    return response()->json([
                        'success' => false,
                        'message' => 'Échec du modification de votre voiture.',
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas l\'autorisation néccéssaire pour modifier cette voiture.',
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du modificationde votre voiture.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
