<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUtilisateurRequest;
use App\Http\Requests\UpdateUtilisateurRequest;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::guard('apiut')->user();
            $voiture_id = $user->voiture->id;
            // dd($voiture_id);
            $reservation = Reservation::where('voiture_id', $voiture_id)->get();
            // dd($reservation);
            if ($reservation) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vos reservation ont ete  modifier avec succes.',
                    'date' => $reservation
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec du recuperation de vos  reservation',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du recuperation du reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function active()
    {
        try {
            $user = Auth::guard('apiut')->user();
            $voiture= $user->voiture;
            if ($voiture) {
                $voiture->disponible=true;
                $voiture->update();
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation Ouvert ',
                    'date' => $voiture
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Echec d\'ouverture',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Echec d\'ouverture des reservations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function inactive()
    {
        try {
            $user = Auth::guard('apiut')->user();
            $voiture= $user->voiture;
            if ($voiture) {
                $voiture->disponible=true;
                $voiture->update();
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation fermé ',
                    'date' => $voiture
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Echec de fermeture',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Echec du fermeture des reservations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

  

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        try {
            if ($reservation) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation ont ete  modifier avec succes.',
                    'date' => $reservation
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec du recuperation du reservation',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du recuperation du reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Reservation $reservation)
    {
        try {
            if ($reservation->Accepted == false) {
                $reservation->Accepted = true;
                $reservation->update();
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation ont ete  modifier avec succes.',
                    'date' => $reservation
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec du modification du reservation',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du modification du reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        try {
            if ($reservation) {
                $reservation->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation ont ete  supprimer avec succes.',
                    'date' => $reservation
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec du suppression du reservation',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du suppression du reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
