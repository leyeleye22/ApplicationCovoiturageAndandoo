<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;
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
            $trajet = Trajet::all();
            if ($trajet) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trajet reccupéré avec succès.',
                    'date' => $trajet
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
     * Store a newly created resource in storage.
     */
    public function store(StoreTrajetRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $trajet = new Trajet();
            $user = Auth::guard('apiut')->user()->id;
            $trajet->fill($validatedData);
            $trajet->utilisateur_id = $user;
            if ($trajet->save()) {
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
            $trajet->fill($validatedData);

            if ($trajet->update()) {
                $success = true;
                $message = 'Trajet modifié avec succès.';
                $data = $trajet;
                $statusCode = 200;
            } else {
                // La sauvegarde a échoué
                $message = 'Échec du modification du trajet';
            }
        } catch (\Exception $e) {
            $message = 'Une erreur s\'est produite lors du modification du trajet.';
        
            $statusCode = 200;
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
            if ($trajet->delete()) {
                $success = true;
                $message = 'Trajet supprimé avec succès.';
                $statusCode = 200;
            } else {
                $message = 'Échec du suppression du trajet';
            }
        } catch (\Exception $e) {
            $message = 'Une erreur s\'est produite lors de la suppression du trajet.';
            $statusCode = 500;
        }

        return response()->json(['success' => $success, 'message' => $message], $statusCode);
    }
}
