<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    // public function register(): void
    // {
    //     $this->reportable(function (\Illuminate\Database\QueryException $e) {
    //         Log::error("Une exception de base de données s'est produite: " . $e->getMessage());
    //         return response()->json([
    //             'error' => 'Une erreur de base de données s\'est produite. Veuillez réessayer plus tard.'
    //         ], $e->getStatusCode() ?: 400);
    //     });

    //     // $this->renderable(function (InvalidArgumentException $e, $request) {
    //     //     return response()->json([
    //     //         'error' => 'Veuillez fournir le bon token',
    //     //         'details' => 'Verifier votre role svp',
    //     //         'url' => 'Cette route ' . ' ' . $request->url() . ' ' . 'ne vous est pas authoriser',
    //     //     ], $e->getStatusCode() ?: 400);
    //     // });
    //     $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
    //         return response()->json([
    //             'error' => 'Vous avez utiliser une mauvaise methode',
    //             'details' => 'La method utiliser n\est pas supporter',
    //             'url' => 'Cette route ' . ' ' . $request->url() . ' ' . 'Supporte pas la methode utiliser',
    //         ], $e->getStatusCode() ?: 400);
    //     });
    //     $this->renderable(function (RouteNotFoundException $e, $request) {
    //         return response()->json([
    //             'error' => 'Veuillez vous connecter svp!!!.',
    //             'details' => 'Soit Vous n\'etes pas connecter soit vous n\'avez les droit d\'acces necessaire',
    //             'url' => 'Cette route ' . ' ' . $request->url() . ' ' . 'vous est interdite',
    //         ], $e->getStatusCode() ?: 400);
    //     });
    //     $this->reportable(function (\Illuminate\Auth\AuthenticationException $e) {
    //         Log::error("Une erreur d'authentification s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Illuminate\Validation\ValidationException $e) {
    //         Log::error("Une exception de validation s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         Log::error("Une exception de modèle non trouvée s'est produite: " . $e->getMessage());
    //     });

    //     $this->renderable(function (\Illuminate\Database\QueryException $e, $request) {
    //         return response()->json([
    //             'message' => 'Erreur de base de données lors du rendu.',
    //             'details' => $e->getMessage(),
    //             'url' => $request->url()
    //         ], $e->getStatusCode() ?: 400);
    //     });

    //     $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
    //         return response()->json([
    //             'error' => 'Erreur d\'authentification lors du rendu.',
    //             'details' => $e->getMessage(),
    //             'url' => $request->url()
    //         ], $e->getStatusCode() ?: 400);
    //     });

    //     $this->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
    //         return response()->json([
    //             'error' => 'Erreur de validation lors du rendu.',
    //             'details' => $e->errors(),
    //             'url' => $request->url()
    //         ], $e->getStatusCode() ?: 400);
    //     });

    //     $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
    //         return response()->json([
    //             'error' => 'Erreur de modèle non trouvée lors du rendu.',
    //             'details' => $e->getMessage(),
    //             'url' => $request->url()
    //         ], $e->getStatusCode() ?: 400);
    //     });
    //     $this->renderable(function (NotFoundHttpException $e, $request) {
    //         $message = $e->getMessage();

    //         if (strpos($message, 'No query results for model') !== false) {
    //             return response()->json([
    //                 'error' => 'Ressource non trouvée',
    //                 'details' => $message,
    //                 'url' => $request->url()
    //             ], 404);
    //         } else {
    //             return response()->json([
    //                 'error' => 'Route non trouvée',
    //                 'details' => 'La route spécifiée n\'existe pas sur ce serveur',
    //                 'url' => $request->url()
    //             ], 404);
    //         }
    //     });
    //     $this->reportable(function (\Illuminate\Database\QueryException $e) {
    //         Log::error("Une exception de base de données s'est produite: " . $e->getMessage());
    //         return response()->json([
    //             'error' => 'Une erreur de base de données s\'est produite. Veuillez réessayer plus tard.'
    //         ], $e->getStatusCode() ?: 400);
    //     });

    //     $this->reportable(function (\Illuminate\Auth\AuthenticationException $e) {
    //         Log::error("Une erreur d'authentification s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Illuminate\Validation\ValidationException $e) {
    //         Log::error("Une exception de validation s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         Log::error("Une exception de modèle non trouvée s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
    //         Log::error("Une exception de fichier non trouvé s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    //         Log::error("Une exception de route non trouvée s'est produite: " . $e->getMessage());
    //     });


    //     $this->reportable(function (\Illuminate\Http\Exceptions\HttpResponseException $e) {
    //         Log::error("Une exception de méthode HTTP non autorisée s'est produite: " . $e->getMessage());
    //     });

    //     $this->reportable(function (\Illuminate\Session\TokenMismatchException $e) {
    //         Log::error("Une exception de token CSRF invalide s'est produite: " . $e->getMessage());
    //     });
    //     $this->reportable(function (DatabaseNotAvailableException $e) {
    //         Log::error("La base de données n'est pas disponible: " . $e->getMessage());
    //     });

    //     $this->reportable(function (ServerNotAvailableException $e) {
    //         Log::error("Le serveur n'est pas disponible: " . $e->getMessage());
    //     });
    // }
}
