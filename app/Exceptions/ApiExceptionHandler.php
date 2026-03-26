<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {

            // Validation
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Données invalides',
                    'errors' => $exception->errors()
                ], 422);
            }

            // Autorisation
            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'message' => $exception->getMessage()
                ], 403);
            }

            // Modèle non trouvé
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Ressource non trouvée'
                ], 404);
            }
            
            // Toutes les autres erreurs
            return response()->json([
                'message' => 'Erreur serveur',
                'error' => $exception->getMessage()
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
