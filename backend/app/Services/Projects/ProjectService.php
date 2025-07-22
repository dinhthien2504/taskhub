<?php

namespace App\Services\Projects;

use App\Repositories\ProjectEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    protected $projectRepository;

    public function __construct(ProjectEloquentRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getAllProjects(?string $search = null, int $perPage = 10, string $status)
    {
        return $this->projectRepository->getAll(['owner'], $search, $perPage, $status);
    }

    public function createProject($request)
    {
        return DB::transaction(function () use ($request) {
            $projectData = [
                'name' => $request->name,
                'owner_id' => $request->owner_id,
                'status' => 'in_progress'
            ];
            if (isset($request->description)) {
                $projectData['description'] = $request->description;
            }
            $project = $this->projectRepository->create($projectData);
            $project->load('owner');
            $project->loadCount('tasks');
            return $project;
        });
    }

    public function updateProject($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $project = $this->projectRepository->find($id);

            $projectData = [
                'name' => $request->name,
                'owner_id' => $request->owner_id,
                'status' => $project->status,
            ];

            if (isset($request->description)) {
                $projectData['description'] = $request->description;
            }

            $this->projectRepository->update($id, $projectData);

            $project->refresh()->load('owner');
            $project->loadCount('tasks');
            return $project;
        });
    }

    public function updateProjectStatus($request, $id)
    {
        $status = $request->status;
        return DB::transaction(function () use ($id, $status) {
            return $this->projectRepository->update($id, [
                'status' => $status
            ]);
        });
    }

}