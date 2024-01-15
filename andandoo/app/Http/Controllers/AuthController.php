<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Utilisateur;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\QueryException;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\RegisterAdminRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginuser']]);
    }
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $utilisateur = new Utilisateur();
            $utilisateur->fill($validatedData);
            $user = Hash::make($utilisateur->password);
            $utilisateur->password = $user;
            if ($utilisateur->save()) {
                return response()->json(['message' => 'User registered successfully', 'user' => $utilisateur], Response::HTTP_CREATED);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to register user. Database error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to register user. Unexpected error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function loginuser(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::guard('apiut')->attempt($credentials)) {
            // $utilisateur = Auth::guard('apiut')->user();
            // $token = $utilisateur->createToken('token-name')->plainTextToken;
            // return $this->respondWithToken($utilisateur, $token);
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $utilisateur = auth()->guard('apiut')->user();
        return $this->respondWithTokens($utilisateur, $token);
    }

    public function RegisterAdmin(RegisterAdminRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $user = new User();
            $user->fill($validatedData);
            if ($user->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur administrateur enregistré avec succès.',
                    'data' => $user, // Vous pouvez inclure des données supplémentaires ici si nécessaire
                ]);
            } else {
                // La sauvegarde a échoué
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'enregistrement de l\'utilisateur administrateur.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'enregistrement de l\'utilisateur administrateur.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();

        return $this->respondWithToken($user, $token);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'utilisateur' => $user,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    protected function respondWithTokens($token, $utilisateur)
    {
        return response()->json([
            'access_token' => $token,
            'utilisateur' => $utilisateur,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    
}
