<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Mail\ResetPassword;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\LoginAdminRequest;
use Illuminate\Support\Facades\Password;
use App\Exceptions\RegistrationException;
use App\Http\Requests\RegisterAdminRequest;
use App\Notifications\SmsValidationAuthentification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login', 'register',
            'loginuser', 'RegisterAdmin', 'VerifMail', 'test'
        ]]);
    }

    public function register(RegisterRequest $request)
    {

        $response = [
            'message' => 'Les images doivent être rempli',
            'user' => null,
            'statusCode' => 400,
        ];

        try {
            $validatedData = $request->validated();
            if (($request->role == "chauffeur") && (!isset($request->ImageProfile) || !isset($request->Licence) || !isset($request->PermisConduire))) {
                return response()->json($response, $response['statusCode']);
            }

            $utilisateur = new Utilisateur();
            $utilisateur->fill($validatedData);
            $this->saveImage($request, 'ImageProfile', 'images/profils', $utilisateur, 'ImageProfile');
            $this->saveImage($request, 'Licence', 'images/licence', $utilisateur, 'Licence');
            $this->saveImage($request, 'PermisConduire', 'images/permis', $utilisateur, 'PermisConduire');
            $utilisateur->password = Hash::make($utilisateur->password);

            if ($utilisateur->save()) {
                $response['message'] = 'Utilisateur inscrit avec succès';
                $response['user'] = $utilisateur;
                $response['statusCode'] = Response::HTTP_CREATED;
            }
        } catch (ValidationException $e) {
            $response['error'] = $e->validator->errors();
            $response['statusCode'] = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (QueryException $e) {
            $response['error'] = 'Erreur d\'inscription de l\'utilisateur. Erreur de base de données.';
        } catch (\Exception $e) {
            $response['error'] = 'Erreur d\'inscription de l\'utilisateur. Erreur système.';
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
    public function loginuser(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $user = User::where('email', $credentials['email'])->first();

            if ($user && $user->TemporaryBlock) {
                $response = ['error' => 'Compte Temporairement bloquer'];
                $statusCode = 403;
            } elseif ($user && $user->PermanentBlock) {
                $response = ['error' => 'Compte Definitivement bloquer'];
                $statusCode = 403;
            } elseif (!$token = Auth::guard('apiut')->attempt($credentials)) {
                throw new \Exception('Vous n\'êtes pas authoriser');
            } else {
                $utilisateur = auth()->guard('apiut')->user();
                $response = $this->respondWithTokens($token, $utilisateur);
                $statusCode = 200;
            }
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                $response = ['error' => $e->errors()];
                $statusCode = 422;
            } else {
                $response = ['error' => $e->getMessage()];
                $statusCode = 401;
            }
        }

        return response()->json($response, $statusCode);
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
            return response()->json(['error' => 'Mot de passe ou email incorrect '], 419);
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

        return response()->json(['status' => 200, 'message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            return response()->json([
                'status' => 'refresh',
                'user' => Auth::user(),
                'Authorization' => [
                    'token' => Auth::token(),
                    'type' => 'bearer'
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(["Error" => "Invalid authorization Token"]);
        }
    }
    public function Torefresh()
    {
        try {
            return response()->json([
                'status' => 'refresh',
                'user' => Auth::guard('apiut')->user(),
                'Authorization' => [
                    'token' => Auth::guard('apiut'),
                    'type' => 'bearer'
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(["Error" => "Invalid authorization Token"]);
        }
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
            'expires_in' => 3600
        ]);
    }
    protected function respondWithTokens($token, $utilisateur)
    {
        return response()->json([
            'access_token' => $token,
            'utilisateur' => $utilisateur,
            'token_type' => 'bearer',
            'expires_in' => 3600
        ]);
    }
    public function blockTemporarilyUser(Utilisateur $user)
    {
        try {
            if ($user->PermanentBlock) {
                return response()->json(['error' => 'User is already permanently blocked'], 400);
            }
            $user->TemporaryBlock = true;
            $user->save();

            return response()->json(['message' => 'User blocked temporarily successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function blockPermanentlyUser(Utilisateur $user)
    {
        try {
            $user->PermanentBlock = true;
            $user->TemporaryBlock = false;
            $user->save();

            return response()->json(['message' => 'User blocked permanently successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function unblockUser(Utilisateur $user)
    {
        $response = ['error' => 'notithing'];

        try {
            if ($user->TemporaryBlock) {
                if ($user->PermanentBlock) {
                    $response = ['error' => 'User cannot be unblocked permanently'];
                } else {
                    $user->TemporaryBlock = false;
                    $user->save();
                    $response = ['message' => 'User unblocked successfully'];
                }
            } else {
                $response = ['error' => 'User cannot be unblocked'];
            }
        } catch (\Exception $e) {
            $response = ['error' => $e->getMessage()];
        }

        return response()->json($response, $response['error'] ? 200 : 200);
    }
}
