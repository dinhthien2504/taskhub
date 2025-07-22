<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectEloquentRepository extends EloquentRepository
{
    public function getModel(): string
    {
        return Project::class;
    }

    public function getAll($with = [], string $search = null, int $perPage = 10, string $status = 'in_progress')
    {
        $query = $this->_model::with($with)->withCount('tasks');

        $query->where('status', $status);

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getProjectsByUser($search = null, $perPage = 10, string $status = 'in_progress')
    {
        $user = auth()->user();
        $assignedProjectIds = $user->projects()->pluck('projects.id');

        $query = $this->_model::query()
            ->where(function ($q) use ($assignedProjectIds, $user) {
                $q->whereIn('id', $assignedProjectIds)
                    ->orWhere('owner_id', $user->id);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        if ($status) {
            $query->where(function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        return $query->latest()->paginate($perPage);
    }

}