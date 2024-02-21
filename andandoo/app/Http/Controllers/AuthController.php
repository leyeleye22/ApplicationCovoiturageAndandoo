<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Utilisateur;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\QueryException;
use App\Http\Requests\RegisterAdminRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'loginuser', 'RegisterAdmin', 'login']]);
    }

    public function register(RegisterRequest $request)
    {

        $response = [
            'message' => 'Les images doivent être rempli',
            'user' => null,
            'statusCode' => 422,
        ];

        // try {
        $validatedData = $request->validated();
        if (
            $request->role == "chauffeur" &&
            (!$request->hasFile('ImageProfile') ||
                !$request->hasFile('Licence') ||
                !$request->hasFile('PermisConduire') ||
                !$request->hasFile('CarteGrise'))
        ) {
            return response()->json($response, $response['statusCode']);
        }


        $utilisateur = new Utilisateur();
        $utilisateur->fill($validatedData);
        $this->saveImage($request, 'ImageProfile', 'images/profils', $utilisateur, 'ImageProfile');
        $this->saveImage($request, 'Licence', 'images/licence', $utilisateur, 'Licence');
        $this->saveImage($request, 'PermisConduire', 'images/permis', $utilisateur, 'PermisConduire');
        $this->saveImage($request, 'CarteGrise', 'images/cartegrise', $utilisateur, 'CarteGrise');
        $utilisateur->password = Hash::make($utilisateur->password);
        $utilisateur->Email = 'Em@em.com';

        if ($utilisateur->save()) {
            Cache::forget('utilisateur');
            Cache::forget('chauffeurs');
            Cache::forget('clients');
            $response['message'] = 'Utilisateur inscrit avec succès';
            $response['user'] = $utilisateur;
            $response['statusCode'] = Response::HTTP_CREATED;
            $codeValidation = $this->generateValidationCode();
            $this->sendWhatsappCodeValidation($utilisateur, $codeValidation);
        }
        // } catch (ValidationException $e) {
        //     $response['error'] = $e->validator->errors();
        //     $response['statusCode'] = Response::HTTP_UNPROCESSABLE_ENTITY;
        // } catch (QueryException $e) {
        //     $response['error'] = 'Erreur d\'inscription de l\'utilisateur. Erreur de base de données.';
        // } catch (\Exception $e) {
        //     $response['error'] = 'Erreur d\'inscription de l\'utilisateur. Erreur système.';
        // }

        return response()->json($response, $response['statusCode']);
    }
    private function sendWhatsappCodeValidation(Utilisateur $user, $codeValidation)
    {
        try {
            $numeroWhatsApp = '781132618';
            $numeroTelephoneUser = $user->Telephone;
            $message = "Votre code de validation est : $codeValidation";
            $urlWhatsApp = "https://api.whatsapp.com/send?phone=$numeroTelephoneUser&text=" . urlencode($message);
            return redirect()->to($urlWhatsApp);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function generateValidationCode($length = 6)
    {
        $characters = '0123456789';
        $characters_length = strlen($characters);
        $validation_code = '';
        for ($i = 0; $i < $length; $i++) {
            $validation_code .= $characters[rand(0, $characters_length - 1)];
        }
        return $validation_code;
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
                $response = [
                    'error' => 'Compte Temporairement bloquer',
                    'status' => 403
                ];
                $statusCode = 403;
            } elseif ($user->etat == false) {
                $response = [
                    'error' => 'Compte inactif',
                    'status' => 403
                ];
            } elseif ($user && $user->PermanentBlock) {
                $response = [
                    'error' => 'Compte Definitivement bloquer',
                    'status' => 403
                ];
                $statusCode = 403;
            } elseif (!$token = Auth::guard('apiut')->attempt($credentials)) {
                throw new \Exception('Email ou password incorrect');
            } else {
                $utilisateur = auth()->guard('apiut')->user();
                $notifications = $utilisateur->unreadNotifications;
                $data = [];
                foreach ($notifications as $notification) {
                    $data[] = [
                        'lieuDepart' => $notification->data['LieuDepart'],
                        'lieuArriver' => $notification->data['LieuArrivee'],
                        'dateDepart' => $notification->data['DateDepart'],
                        'heureDepart' => $notification->data['HeureDepart'],
                        'prix' => $notification->data['Prix'],
                        'chauffeur' => $notification->data['Chauffeur'],
                    ];
                }
                $response = $this->respondWithTokens($token, $utilisateur, $data[0]);
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

        // Renvoyer la réponse avec le code d'état
        return response()->json($response, $statusCode ?? 500);
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
            return response()->json(['error' => 'Mot de passe ou email incorrect'], 403);
        }

        $user = auth()->user();
        $response = $this->respondWithToken($token, $user);
        return response()->json($response);
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
            'data' => [
                'access_token' => $token,
                'utilisateur' => $user,
                'statusCode' => 200,
                'token_type' => 'bearer',
                'expires_in' => 3600
            ]
        ]);
    }
    protected function respondWithTokens($token, $utilisateur, $notification)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'utilisateur' => $utilisateur,
                'notification' => $notification,
                'statusCode' => 200,
                'token_type' => 'bearer',
                'expires_in' => 3600
            ]
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
