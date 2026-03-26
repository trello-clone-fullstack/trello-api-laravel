<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskPositionRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Afficher toutes les tâches de l'utilisateur connecté.
     */
    public function index($projectId = null)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $query = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['project', 'status']);

        if ($projectId) {
            $project = Project::where('id', $projectId)
                                ->where('user_id', $user->id)
                                ->firstOrFail();

            $query->where('project_id', $projectId);
        }

        $tasks = $query->orderBy('position')->orderBy('created_at')->get();

        return TaskResource::collection($tasks);
    }

    /**
     * Créer une tâche spécifique.
     */
    public function store(StoreTaskRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $project = Project::where('id', $request->project_id)
                        ->where('user_id', $user->id)
                        ->firstOrFail();

        $status = Status::where('status_name', 'À faire')
                        ->where('user_id', $user->id)
                        ->firstOrFail();

        $maxPosition = Task::where('project_id', $request->project_id)->max('position') ?? 0;
        $position = $maxPosition + 1;

        $task = Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'position' => $position,
            'project_id' => $request->project_id,
            'status_id' => $status->id
        ]);

        $task->load(['project', 'status']);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Afficher une tâche spécifique.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $task = Task::where('id', $id)
                    ->whereHas('project', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->with(['project', 'status'])
                    ->firstOrFail();

        return new TaskResource($task);
    }

    /**
     * Met à jour une tâche spécifique.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $task = Task::where('id', $id)
                    ->whereHas('project', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->firstOrFail();

        if ($request->has('status_id')) {
            $status = Status::where('id', $request->status_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();
        }

        $task->update($request->validated());

        $task->load(['project', 'status']);

        return new TaskResource($task);
    }

    /**
     * Met à jour la position et/ou le statut d'une tâche (pour le drag and drop).
     */
    public function updatePosition(UpdateTaskPositionRequest $request, string $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $validated = $request->validated();

        $task = Task::where('id', $id)
                    ->whereHas('project', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->firstOrFail();

        $oldStatusId = $task->status_id;
        $newStatusId = $validated['status_id'];
        $newPosition = $validated['position'];

        // Vérifier que le nouveau statut appartient à l'utilisateur
        $status = Status::where('id', $newStatusId)
                        ->where('user_id', $user->id)
                        ->firstOrFail();

        // Cas 1: Déplacement dans le même statut
        if ($oldStatusId == $newStatusId) {
            // Récupérer toutes les tâches du même statut sauf la tâche actuelle
            $otherTasks = Task::where('project_id', $task->project_id)
                            ->where('status_id', $newStatusId)
                            ->where('id', '!=', $task->id)
                            ->orderBy('position')
                            ->get();

            // Réorganiser les positions
            $positionCounter = 1;
            foreach ($otherTasks as $otherTask) {
                if ($positionCounter == $newPosition) {
                    $positionCounter++; // Sauter la position de la tâche déplacée
                }
                $otherTask->update(['position' => $positionCounter]);
                $positionCounter++;
            }
        }
        // Cas 2: Déplacement vers un nouveau statut
        else {
            // Décaler les tâches de l'ancien statut
            Task::where('project_id', $task->project_id)
                ->where('status_id', $oldStatusId)
                ->where('position', '>', $task->position)
                ->decrement('position');

            // Décaler les tâches du nouveau statut pour faire de la place
            Task::where('project_id', $task->project_id)
                ->where('status_id', $newStatusId)
                ->where('position', '>=', $newPosition)
                ->increment('position');
        }

        // Mettre à jour la tâche
        $task->update([
            'status_id' => $newStatusId,
            'position' => $newPosition
        ]);

        $task->load(['project', 'status']);

        return response()->json([
            'message' => 'Position mise à jour avec succès',
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Supprime une tâche spécifique.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Trouver la tâche avec vérification d'appartenance
        $task = Task::where('id', $id)
                    ->whereHas('project', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->firstOrFail();

        $task->delete();

        return response()->json([
            'message' => 'Tâche supprimée avec succès'
        ]);
    }
}
