<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAvisRequest;
use App\Http\Requests\UpdateAvisRequest;
use App\Models\Utilisateur;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
class AvisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

  public function create(StoreAvisRequest $req)
{
    $success = false;
    $responseData = [];
    $statusCode = 500;

    try {
        $validatedData = $req->validated();
        $avis = new Avis();
        $avis->fill($validatedData);
        $avis->utilisateur_id = Auth::guard('apiut')->user()->id;
        $avis->save();
        $success = true;
        $responseData = ['success' => true, 'avis' => $avis];
        $statusCode = 200;
    } catch (ValidationException $e) {
        $responseData = ['success' => false, 'errors' => $e->errors()];
        $statusCode = 422;
    } catch (QueryException $e) {
        $responseData = ['success' => false, 'error' => 'Erreur de base de données.'];
        $statusCode = 500;
    } catch (\Exception $e) {
        $responseData = ['success' => false, 'error' => $e->getMessage()];
        $statusCode = 500;
    }

    return response()->json($responseData, $statusCode);
}

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show(Utilisateur $utilisateur)
    {
        // try {
        //      $avis=Avis
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avis $avis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAvisRequest $request, Avis $avis)
    {
        $success = false;
        $responseData = [];
        $statusCode = 500;
    
        try {
            $validatedData = $request->validated();
            if ($avis->utilisateur_id !== Auth::guard('apiut')->user()->id) {
                throw new \Exception('Vous n\'êtes pas autorisé à modifier cet avis.');
            }
            $avis->update($validatedData);
            $success = true;
            $responseData = ['success' => true, 'message' => 'L\'avis a été mis à jour avec succès.'];
            $statusCode = 200;
        } catch (ValidationException $e) {
            $responseData = ['success' => false, 'errors' => $e->errors()];
            $statusCode = 422;
        } catch (QueryException $e) {
            $responseData = ['success' => false, 'error' => 'Erreur de base de données lors de la mise à jour.'];
            $statusCode = 500;
        } catch (\Exception $e) {
            $responseData = ['success' => false, 'error' => $e->getMessage()];
            $statusCode = 403;
        }
    
        return response()->json($success,$statusCode,$responseData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avis $avis)
    {
        //
    }
}
