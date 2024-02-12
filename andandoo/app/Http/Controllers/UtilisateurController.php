<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use App\Models\Reservation;
use App\Models\Utilisateur;
use App\Events\ReservationAccepted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreUtilisateurRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateUtilisateurRequest;


class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            $chauffeur = Auth::guard('apiut')->user();

            $voiture = $chauffeur->voiture;

            $trajets = $voiture->trajet;

            $reservations = collect();

            foreach ($trajets as $trajet) {
                $reservationsDuTrajet = $trajet->reservations;
                $reservations = $reservations->concat($reservationsDuTrajet);
            }

            $data = [];
            foreach ($reservations as $reservation) {
                $client = $reservation->utilisateur;
                $trajet = $reservation->trajet;
                $data[] = [
                    'id' => $reservation->id,
                    'Profile' => $client->ImageProfile,
                    'Nom' => $client->Nom,
                    'Prenom' => $client->Prenom,
                    'LieuDepart' => $trajet->LieuDepart,
                    'LieuArrivee' => $trajet->LieuArrivee,
                    'HeureD' => $trajet->HeureD,
                    'DateDepart' => $trajet->DateDepart,
                ];
            }
            if ($reservations) {
                return response()->json($data);
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

    public function showUsers()
    {
        try {
            $users = Utilisateur::all();
            $data = [];

            foreach ($users as $user) {
                $nom = $user['zone']->NomZ;
                $data[] = [

                    'Nom' => $user['Nom'],
                    'Prenom' => $user['Prenom'],
                    'Telephone' => $user['Telephone'],
                    'Email' => $user['Email'],
                    'Image' => $user['ImageProfile'],
                    'Licence' => $user['Licence'],
                    'PermisConduire' => $user['PermisConduire'],
                    'role' => $user['role'],
                    'Zone' => $nom
                ];
            }
            if ($users) {

                return response()->json($data, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function showChauffeur()
    {
        try {
            $chauffeurs = Utilisateur::where('role', 'chauffeur')->get();
            $data = [];

            foreach ($chauffeurs as $chauffeur) {
                $nom = $chauffeur['zone']->NomZ;
                $data[] = [
                    'id' => $chauffeur['id'],
                    'Nom' => $chauffeur['Nom'],
                    'Prenom' => $chauffeur['Prenom'],
                    'Telephone' => $chauffeur['Telephone'],
                    'Email' => $chauffeur['Email'],
                    'Image' => $chauffeur['ImageProfile'],
                    'Licence' => $chauffeur['Licence'],
                    'PermisConduire' => $chauffeur['PermisConduire'],
                    'role' => $chauffeur['role'],
                    'Zone' => $nom,
                    'BlockerTemporairement' => $chauffeur['TemporaryBlock'],
                    'BlockerDefinitivement' => $chauffeur['PermanentBlock']
                ];
            }
            if ($chauffeurs) {

                return response()->json($data, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showClient()
    {
        try {
            $clients = Utilisateur::where('role', 'client')->get();
            $data = [];

            foreach ($clients as $client) {
                $nom = $client['zone']->NomZ;
                $data[] = [
                    'id' => $client['id'],
                    'Nom' => $client['Nom'],
                    'Prenom' => $client['Prenom'],
                    'Telephone' => $client['Telephone'],
                    'Email' => $client['Email'],
                    'Image' => $client['ImageProfile'],
                    'role' => $client['role'],
                    'Zone' => $nom
                ];
            }
            if ($clients) {

                return response()->json($data, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible',
                'error' => $e->getMessage()
            ]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
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
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            if ($reservation->trajet->voiture->disponible) {
                if ($reservation->Accepted) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible d\'accepter cette reservation deux fois',
                    ]);
                }
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
    public function nbruser()
    {
        try {
            $nombreChauffeur = Utilisateur::where('role', 'chauffeur')->count();
            $nombreClient = Utilisateur::where('role', 'client')->count();
            $nombreUtilisateurTotal = Utilisateur::count();
            return response()->json([
                'nombreChauffeur' => $nombreChauffeur,
                'nombreClient' => $nombreClient,
                'nombreUtilisateurTotal' => $nombreUtilisateurTotal
            ]);
        } catch (\Exception $e) {
            logger()->error('Erreur de recuperation du nombre des utilisateur: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur de calcul'], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        try {
            if (Auth::guard('apiut')->user()->role == "client") {
                return response()->json([
                    'message' => "Vous n\'etes pas authoriser",
                    'SatusCode' => 403
                ]);
            }
            if ($reservation->Accepted == false) {
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
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du suppression du reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function logout()
    {
        auth()->guard('apiut')->logout();

        return response()->json(['message' => 'Deconnexion reussi']);
    }
    public function updateProfile(UpdateUtilisateurRequest $request, Utilisateur $utilisateur)
    {
        $response = [
            'message' => 'Les images doivent être rempli',
            'user' => null,
            'statusCode' => 422,
        ];

        try {
            if (Auth::guard('apiut')->user()->id != $utilisateur->id) {
                return response()->json([
                    'message' => 'Vous n\'avez les droits de modification'
                ]);
            }
            $validatedData = $request->validated();
            $utilisateur->fill($validatedData);
            $this->saveImage($request, 'ImageProfile', 'images/profils', $utilisateur, 'ImageProfile');
            $this->saveImage($request, 'Licence', 'images/licence', $utilisateur, 'Licence');
            $this->saveImage($request, 'PermisConduire', 'images/permis', $utilisateur, 'PermisConduire');
            $this->saveImage($request, 'CarteGrise', 'images/cartegrise', $utilisateur, 'CarteGrise');

            if ($utilisateur->update()) {
                $response['message'] = 'Profile modifier avec succès';
                $response['user'] = $utilisateur;
                $response['statusCode'] = Response::HTTP_CREATED;
            }
        } catch (ValidationException $e) {
            $response['error'] = $e->validator->errors();
            $response['statusCode'] = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (QueryException $e) {
            $response['error'] = 'Erreur du modification de l\'utilisateur. Erreur de base de données.';
        } catch (\Exception $e) {
            $response['error'] = 'Erreur du modification de l\'utilisateur. Erreur système.';
        }

        return response()->json($response, $response['statusCode']);
    }
    private function saveImage($request, $fileKey, $path, $utilisateur, $fieldName)
    {
        if ($request->file($fileKey)) {
            $file = $request->file($fileKey);
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path($path), $filename);
            $utilisateur->$fieldName = $filename;
        }
    }
}
