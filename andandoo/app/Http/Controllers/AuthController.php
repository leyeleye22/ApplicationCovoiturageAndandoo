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
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginuser', 'RegisterAdmin']]);
    }
   
    public function register(RegisterRequest $request)
    {
        $response = [
            'message' => '',
            'user' => null,
            'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ];

        try {
            $validatedData = $request->validated();
            $utilisateur = new Utilisateur();
            $utilisateur->fill($validatedData);
            $user = Hash::make($utilisateur->password);
            $utilisateur->password = $user;

            if ($utilisateur->save()) {
                $response['message'] = 'User registered successfully';
                $response['user'] = $utilisateur;
                $response['statusCode'] = Response::HTTP_CREATED;
            }
        } catch (ValidationException $e) {
            $response['error'] = $e->validator->errors();
            $response['statusCode'] = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (QueryException $e) {
            $response['error'] = 'Failed to register user. Database error.';
        } catch (\Exception $e) {
            $response['error'] = 'Failed to register user. Unexpected error.';
        }

        return response()->json($response, $response['statusCode']);
    }


    public function loginuser(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::guard('apiut')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $utilisateur = auth()->guard('apiut')->user();
        return $this->respondWithTokens($token, $utilisateur);
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
                    'data' => $user,
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

        return $this->respondWithToken($token, $user); 
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
        return $this->respondWithToken(auth()->refreshToken(), Auth::user());
    }
    public function Torefresh()
    {
        return $this->respondWithTokens(auth('apiut')->refreshToken(), Auth::guard('apiut')->user());
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
            'expires_in' => auth('apiut')->manager()->getTTL() * 60
        ]);
    }
}
