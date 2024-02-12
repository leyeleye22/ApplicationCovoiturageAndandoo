<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function listUsers()
    {

        try {
            $user = Cache::remember('utilisateur', 3600, function () {
                return Utilisateur::all();
            });
            return response()->json(['data' => $user], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Failed to retrieve message. Unexpected error.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
