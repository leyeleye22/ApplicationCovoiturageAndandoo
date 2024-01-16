<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $reservation = Reservation::where('utilisateur_id', Auth::guard('apiut')->user()->id)->get();
            if ($reservation) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vos trajet ont été  reccupéré avec succès.',
                    'date' => $reservation
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec du recuperation de vos  trajet',
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $voiture = Voiture::where('id', $validatedData["voiture_id"])->first();
            if ($voiture->disponible) {

                $reservation = new  Reservation();
                $reservation->fill($validatedData);
                $reservation->voiture_id = $validatedData["voiture_id"];
                $reservation->utilisateur_id = Auth::guard('apiut')->user()->id;

                if ($reservation->save()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Votre treservation est en cours de validation',
                        'date' => $reservation
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Échec du reservation',
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation indisponible'

                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'enregistrement de votre reservation.',
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
            if ($reservation->utilisateur_id == Auth::guard('apiut')->user()->id) {
                if ($reservation) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Votre reservation ',
                        'date'=>$reservation
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Échec du recupperation',
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de recuperatuion cette reservation'

                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la recuperation de votre reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        try {
            $validatedData = $request->validated();

            if ($reservation->utilisateur_id == Auth::guard('apiut')->user()->id) {
                $reservation->fill($validatedData);
                $reservation->voiture_id = $validatedData["voiture_id"];
                $reservation->utilisateur_id = Auth::guard('apiut')->user()->id;
                if ($reservation->update()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Votre reservation a été modifié',
                        'date' => $reservation
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Échec du modification',
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas authoriser à modifier cette reservation'

                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'enregistrement de votre reservation.',
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
            if ($reservation->utilisateur_id == Auth::guard('apiut')->user()->id) {
                if ($reservation->delete()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Votre reservation a été supprimé avec succés',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Échec du suppression',
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer cette reservation'

                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la suppression de votre reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
