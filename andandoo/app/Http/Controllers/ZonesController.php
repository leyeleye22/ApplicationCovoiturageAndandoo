<?php

namespace App\Http\Controllers;

use App\Models\Zones;
use App\Http\Requests\StoreZonesRequest;
use App\Http\Requests\UpdateZonesRequest;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ZonesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function create(StoreZonesRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $zones = new Zones();
            $user = auth()->user()->id;
            $zones->NomZ = $validatedData['nom'];
            $zones->user_id = $user;
            $zones->save();

            return response()->json(['message' => 'Zone created successfully'], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $errorMessage = 'Failed to create zone. Database error.';
        } catch (\Exception $e) {
            $errorMessage = 'Failed to create zone. Unexpected error.';
        }

        return response()->json(['error' => $errorMessage ?? 'Unknown error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function show()
    {
        try {
            // Fetch and return the list of zones
            $zones = Zones::all();
            return response()->json(['data' => $zones], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve zones. Unexpected error.'],
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateZonesRequest $request, Zones $zones)
    {
        try {
            $validatedData = $request->validated();
            $zones->NomZ = $validatedData['nom'];
            $zones->save();

            return response()->json(['message' => 'Zone updated successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Zone not found.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update zone. Unexpected error.'],
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Zones $zones)
    {
        try {
            $zones->delete();
            return response()->json(['message' => 'Zone deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Zone not found.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete zone. Unexpected error.'],
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
