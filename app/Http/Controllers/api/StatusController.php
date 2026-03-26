<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Status;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusResource;
use Illuminate\Support\Facades\Auth;

    class StatusController extends Controller
{   /** Afficher tous les statuts de l'utilisateur connecté.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $statuses = $user->statuses()->orderBy('id')->get();
        return response()->json(StatusResource::collection($statuses));
    }
/** Créer un nouveau statut pour l'utilisateur connecté.
     */
    public function store(StoreStatusRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Création via la relation pour la sécurité
        $status = $user->statuses()->create($request->validated());

        return response()->json([
            'message' => 'Status créé avec succès',
            'data' => new StatusResource($status)
        ], 201);
    }

    /** Afficher un statut spécifique */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $status = $user->statuses()->find($id);

        if (!$status) {
            return response()->json(['message' => 'Statut non trouvé'], 404);
        }

        return response()->json(new StatusResource($status));
    }

    /** Mettre à jour un statut */
    public function update(UpdateStatusRequest $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $status = $user->statuses()->find($id);

        if (!$status) {
            return response()->json(['message' => 'Statut non trouvé'], 404);
        }

        $status->update($request->validated());

        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'data' => new StatusResource($status)
        ]);
    }

    /** Supprimer un statut */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $status = $user->statuses()->find($id);

        if (!$status) {
            return response()->json(['message' => 'Statut non trouvé'], 404);
        }

        $status->delete();

        return response()->json(['message' => 'Statut supprimé avec succès']);
    }
}

