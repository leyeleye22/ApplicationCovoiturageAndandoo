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
        $response = [
            'success' => false,
            'message' => '',
            'data' => null,
            'statusCode' => 500,
        ];

        try {
            $user = Auth::guard('apiut')->user();
            $voiture = $user->voiture;

            if (isset($voiture)) {
                $response['message'] = 'Vous avez déjà ajouté une voiture';
            } else {
                $validatedData = $request->validated();
                $voiture = new Voiture();
                $voiture->fill($validatedData);
                $this->saveImage($request, 'ImageVoitures', 'images/voiture', $voiture, 'ImageVoitures');

                $voiture->utilisateur_id = $user->id;

                if ($voiture->save()) {
                    $response['success'] = true;
                    $response['message'] = 'Votre voiture a été enregistrée avec succès.';
                    $response['data'] = $voiture;
                    $response['statusCode'] = 200;
                } else {
                    $response['message'] = 'Échec de l\'enregistrement de votre voiture.';
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'Une erreur s\'est produite lors de l\'enregistrement de votre voiture.';
            $response['error'] = $e->getMessage();
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




    public function update(UpdateVoitureRequest $request, Voiture $voiture)
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => null,
            'statusCode' => 500,
        ];

        try {
            if (Auth::guard('apiut')->user()->id == $voiture->utilisateur_id) {
                $validatedData = $request->validated();
                $voiture->fill($validatedData);

                if ($voiture->save()) {
                    $response['success'] = true;
                    $response['message'] = 'Votre voiture a été modifiée avec succès.';
                    $response['data'] = $voiture;
                    $response['statusCode'] = 200;
                } else {
                    $response['message'] = 'Échec de la modification de votre voiture.';
                }
            } else {
                $response['message'] = 'Vous n\'avez pas l\'autorisation nécessaire pour modifier cette voiture.';
                $response['statusCode'] = 403;
            }
        } catch (\Exception $e) {
            $response['message'] = 'Une erreur s\'est produite lors de la modification de votre voiture.';
            $response['error'] = $e->getMessage();
        }

        return response()->json($response, $response['statusCode']);
    }
    public function showVoitureD()
    {
        try {
            $voiture = Voiture::where('disponible', true)->get();
            if ($voiture) {
                return response()->json([
                    'message' => 'success',
                    'StatusCode' => 200,
                    'Data' => $voiture
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function showVoitureInd()
    {
        try {
            $voiture = Voiture::where('disponible', false)->get();
            if ($voiture) {
                return response()->json([
                    'message' => 'success',
                    'StatusCode' => 200,
                    'Data' => $voiture
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function showVoiture()
    {
        try {
            $voiture = Voiture::all();
            if ($voiture) {
                return response()->json([
                    'message' => 'success',
                    'StatusCode' => 200,
                    'Data' => $voiture
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible',
                'error' => $e->getMessage()
            ]);
        }
    }
}
