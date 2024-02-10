<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Trajet;
use Carbon\Carbon;

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
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => null,
            'statusCode' => 500,
        ];

        try {
            $validatedData = $request->validated();
            $trajet = Trajet::with('voiture')->findOrFail($validatedData["trajet_id"]);
            if ($trajet->DateDepart > Carbon::now()) {
                $response = [
                    'success' => false,
                    'message' => 'Réservation indisponible : la date est expiree',
                    'statusCode' => 400,
                ];
            }
            if (!$trajet->voiture->disponible) {
                $response = [
                    'success' => false,
                    'message' => 'Réservation indisponible : la voiture n\'est pas disponible',
                    'statusCode' => 400,
                ];
            } elseif ($validatedData['NombrePlaces'] > $trajet->voiture->NbrPlaces) {
                $response = [
                    'success' => false,
                    'message' => 'Impossible de réserver plus de places que celles disponibles dans la voiture',
                    'statusCode' => 419,
                ];
            } elseif ($trajet->reservations()->where('utilisateur_id', $request->user()->id)->exists()) {
                $response = [
                    'success' => false,
                    'message' => 'Vous avez déjà une réservation sur ce trajet.',
                    'statusCode' => 400,
                ];
            } else {
                $reservation = new Reservation($validatedData);
                $reservation->trajet()->associate($trajet);
                $reservation->utilisateur()->associate($request->user());

                if ($reservation->save()) {
                    $response = [
                        'success' => true,
                        'message' => 'Votre réservation est en cours de validation',
                        'data' => $reservation,
                        'statusCode' => 200,
                    ];
                } else {
                    $response['message'] = 'Échec de la réservation';
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'Une erreur s\'est produite lors de l\'enregistrement de votre réservation.';
            $response['error'] = $e->getMessage();
        }

        return response()->json($response, $response['statusCode']);
    }






    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => null,
            'statusCode' => 500,
        ];

        try {
            if ($reservation->utilisateur_id == Auth::guard('apiut')->user()->id) {
                if ($reservation) {
                    $response = [
                        'success' => true,
                        'message' => 'Votre réservation',
                        'data' => $reservation,
                        'statusCode' => 200,
                    ];
                } else {
                    $response['message'] = 'Échec de la récupération';
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Impossible de récupérer cette réservation',
                    'statusCode' => 403,
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la récupération de votre réservation.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response, $response['statusCode']);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => null,
            'statusCode' => 500,
        ];

        try {
            $validatedData = $request->validated();

            if ($reservation->utilisateur_id == Auth::guard('apiut')->user()->id) {
                $reservation->fill($validatedData);
                $reservation->utilisateur_id = Auth::guard('apiut')->user()->id;

                if ($reservation->update()) {
                    $response = [
                        'success' => true,
                        'message' => 'Votre réservation a été modifiée',
                        'data' => $reservation,
                        'statusCode' => 200,
                    ];
                } else {
                    $response['message'] = 'Échec de la modification';
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à modifier cette réservation',
                    'statusCode' => 403,
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la modification de votre réservation.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response, $response['statusCode']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $response = [
            'success' => false,
            'message' => '',
            'statusCode' => 500,
        ];

        try {
            if ($reservation->utilisateur_id == Auth::guard('apiut')->user()->id) {
                if ($reservation->delete()) {
                    $response = [
                        'success' => true,
                        'message' => 'Votre réservation a été supprimée avec succès',
                        'statusCode' => 200,
                    ];
                } else {
                    $response['message'] = 'Échec de la suppression';
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Impossible de supprimer cette réservation',
                    'statusCode' => 403,
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la suppression de votre réservation.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response, $response['statusCode']);
    }
    public function delete()
    {

        $response = [
            'success' => false,
            'message' => '',
            'statusCode' => 500,
        ];

        try {
            $reservations = Reservation::where('utilisateur_id', Auth::guard('apiut')->user()->id)->get();
            if ($reservations) {
                foreach ($reservations as $reservation) {
                    $reservation->delete();
                }
                $response = [
                    'success' => true,
                    'message' => 'Votre réservation a été supprimée avec succès',
                    'statusCode' => 200,
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Impossible de supprimer cette réservation',
                    'statusCode' => 403,
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la suppression de votre réservation.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response, $response['statusCode']);
    }
}
