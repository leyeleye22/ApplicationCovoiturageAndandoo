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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register',
        'loginuser', 'RegisterAdmin', 'VerifMail', 'test']]);
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
        try {
            $credentials = $request->only(['email', 'password']);
            $user = User::where('email', $credentials['email'])->first();

            if ($user && $user->TemporaryBlock) {
                $response = ['error' => 'Account temporarily blocked'];
                $statusCode = 403;
            } elseif ($user && $user->PermanentBlock) {
                $response = ['error' => 'Account permanently blocked'];
                $statusCode = 403;
            } elseif (!$token = Auth::guard('apiut')->attempt($credentials)) {
                throw new \Exception('Unauthorized');
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
        $response = [];

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
    public function generateVerificationCode()
    {
        return mt_rand(100000, 999999);
    }
    public function VerifMail(Request $req)
    {
        try {
            $email = $req->only('email');
            $user = User::where('email', $email)->first();
            if ($user) {
                $codeverif = mt_rand(100000, 999999);
                Mail::to($email)->send(new ResetPassword($codeverif));
                Session::put('codeverif', $codeverif);
                return response()->json([
                    'Status' => 'Succés',
                    'Message' => 'Mail envoyé avec succés'
                ]);
            } else {
                return response()->json([
                    'Status code' => 'error',
                    'Message' => 'Utilisateur non trouvé'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'Status code' => 'error',
                'Message' => $e->getMessage()
            ]);
        }
    }
    public function test()
    {
        dd(Session::all());
        return response()->json([
            'Status' => 'Succés',
            'Message' => Session::get('codeverif')
        ]);
    }
}