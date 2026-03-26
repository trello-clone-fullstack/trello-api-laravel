<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /** Afficher tous les projets de l'utilisateur connecté.
     */
    public function index()
    {
        $projects = Auth::user()->projects;
        return ProjectResource::collection($projects);
    }

    /** Créer un nouveau projet pour l'utilisateur connecté.
     */
    public function store(ProjectRequest $request)
    {
        $project = Auth::user()->projects()->create($request->validated());
        return new ProjectResource($project);
    }

    /** Affiche un projet précis de l'utilisateur connecté.
     */
    public function show(Project $project)
    {
        $this->authorizeAccess($project);
        return new ProjectResource($project);
    }

    /** Met à jour un projet
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorizeAccess($project);
        $project->update($request->validated());
        return new ProjectResource($project);
    }

    /** Supprime un projet
     */
    public function destroy(Project $project)
    {
        $this->authorizeAccess($project);
        $project->delete();
        return response()->json(['message' => 'Projet supprimé'], 200);
    }

    /** Vérifie que l'utilisateur est propriétaire du projet
     */
    private function authorizeAccess(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            throw new AuthorizationException(response()->json([
                'message' => 'Vous n\'avez pas accès à ce projet.'
            ], 403));
        }
    }
}
