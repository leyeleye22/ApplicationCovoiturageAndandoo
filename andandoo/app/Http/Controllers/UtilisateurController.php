<?php

namespace App\Http\Controllers;

use App\Events\ReservationAccepted;
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
            $user = Auth::guard('apiut')->user()->id;
            $reservation = Reservation::where('utilisateur_id', $user)->get();
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
            if ($reservation->trajet->voiture->disponible) {
                $reservation->Accepted = true;
                $reservation->update();
                event(new ReservationAccepted($reservation));
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation ont ete  modifier avec succes.',
                    'date' => $reservation
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'accepter un reservation votre voiture est pleine',
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
                    'message' => 'Reservation a ete  supprimer avec succes.',
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
