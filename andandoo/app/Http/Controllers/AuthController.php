<?php

namespace App\Http\Controllers;


use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
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
        $this->middleware('auth:api', ['except' => ['register', 'loginuser', 'RegisterAdmin', 'login', 'sendwhatsappcode', 'showFormValidationCodeWhatsappp', 'submitValidationForm']]);
    }

    public function register(RegisterRequest $request)
    {

        $response = [
            'message' => 'Les images doivent être rempli',
            'user' => null,
            'statusCode' => 422,
        ];

        try {
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

            if ($utilisateur->save()) {
                Cache::forget('utilisateur');
                Cache::forget('chauffeurs');
                Cache::forget('clients');
                $response['message'] = 'Utilisateur inscrit avec succès';
                $response['user'] = $utilisateur;
                $response['statusCode'] = Response::HTTP_OK;
                if ($utilisateur->role == "client") {
                    $codeValidation = $this->generateValidationCode();
                    return redirect()->route('whatsapp', ['user' => $utilisateur->id, 'codeValidation' => $codeValidation]);
                }
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
    public function showFormValidationCodeWhatsappp($token)
    {
        return view('formvalide', ['token' => $token]);
    }
    public function sendwhatsappcode(Utilisateur $user, $codeValidation)
    {
        try {
            $numeroWhatsApp = $user->Telephone;
            $token = Str::random(32);
            DB::table('password_reset_tokens')->insert([
                'email' => $user->Email,
                'token' => $token,
                'codeValidation' => $codeValidation,
                'created_at' => Carbon::now(),
            ]);
            $lien = route('ValidationCodeWhatsappp', ['token' => $token]);
            $message = "Voici votre code de validation WhatsApp : $codeValidation. Pour valider, veuillez cliquer sur le lien suivant : $lien";
            $message = $codeValidation;
            $params = array(
                'token' => 'd3jivu8d0q84v6x5',
                'to' => $numeroWhatsApp,
                'body' => "Voici votre code de validation WhatsApp : $codeValidation. Pour valider, veuillez cliquer sur le lien suivant : $lien"
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance79098/messages/chat",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return redirect()->route('whatsapp')->withErrors(['error' => "Erreur lors de l'envoi du message WhatsApp."]);
            } else {
                return redirect()->to("https://api.whatsapp.com/send?phone=$numeroWhatsApp&text=" . urlencode($message));
            }
        } catch (Exception $e) {
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
    public function submitValidationForm(Request $request)
    {
        $codeexists = DB::table('password_reset_tokens')
            ->where([
                'token' => $request->token,
            ])
            ->first();

        if (!$codeexists) {
            return response()->json(['error' => 'Ressouces introuvables'], 404);
        }
        $user = DB::table('password_reset_tokens')->where(['token' => $request->token])->first();
        Utilisateur::where('Email', $user->email)
            ->update(['etat' => true]);
        DB::table('password_reset_tokens')->where(['token' => $request->token])->delete();

        return response()->json(['message' => 'Votre compte a ete active']);
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
            $user = Utilisateur::where('Email', $credentials['email'])->first();

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
        //login
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
    public function activerCompte(Utilisateur $user)
    {
        try {
            if ($user != null && $user->role == "chauffeur") {
                $user->etat = true;
                $user->save();
                Cache::forget('utilisateur');
                Cache::forget('chauffeurs');
                Cache::forget('clients');
                return response()->json(['Message' => 'compte activer avec succes'], 201);
            }
        } catch (Exception $e) {
        }
    }
}
