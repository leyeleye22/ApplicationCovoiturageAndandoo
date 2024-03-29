<?php

namespace App\Http\Controllers;

use App\Models\Zones;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\StoreZonesRequest;
use App\Http\Requests\UpdateZonesRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ZonesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['show']]);
    }

    public function create(StoreZonesRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $zones = new Zones();

            $zones->NomZ = $request->NomZ;
            $zones->user_id = auth()->user()->id;
            $zones->save();
            Cache::forget('zones');
            return response()->json(['message' => 'Zone creer avec succés'], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $errorMessage = 'Echec de creation du zone. Erreur de base de donnée.';
        } catch (\Exception $e) {
            $errorMessage = 'Echec de creation du zone. Erreur System.';
        }

        return response()->json(['error' => $errorMessage ?? 'Unknown error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function show()
    {
        try {
            $zones = Cache::rememberForever('zones', function () {
                return Zones::all();
            });
            $data = [];
            foreach ($zones as $zone) {
                $data[] = [
                    'id' => $zone['id'],
                    'nom' => $zone['NomZ'],
                ];
            }

            return response()->json($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Echec de recuperation des zones. Consulter erreur.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(UpdateZonesRequest $request, Zones $zones)
    {
        try {

            $zones->NomZ = $request->NomZ;
            $zones->save();
            Cache::forget('zones');
            return response()->json(['message' => 'Zone modifié avec succés'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Zone non trouvé.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Echec de de modification du zone. Consulter  Erreur.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function delete(Zones $zones)
    {
        try {
            $zones->delete();
            Cache::forget('zones');
            return response()->json(['message' => 'Zone supprimer avec succés'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Zone non trouvé.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Echec de suppression du zone.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
