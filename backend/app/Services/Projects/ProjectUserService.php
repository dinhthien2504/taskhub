<?php
namespace App\Services\Projects;

use App\Repositories\ProjectEloquentRepository;

class ProjectUserService
{
    protected $projectRepository;

    public function __construct(ProjectEloquentRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getUsers($projectId)
    {
        $project = $this->projectRepository->find($projectId);
        return $project->users;
    }

    public function assignUsers($projectId, array $userIds)
    {
        $project = $this->projectRepository->find($projectId);
        $project->users()->syncWithoutDetaching($userIds);
        return true;
    }

    public function removeUser($projectId, $userId)
    {
        $project = $this->projectRepository->find($projectId);
        $project->tasks()->where('assigned_to', $userId)->update(['assigned_to' => null]);
        $project->users()->detach($userId);
        return true;
    }

    public function getProjectsByUser($request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10);
        $status = $request->query('status', 'in_progress');

        $projects = $this->projectRepository->getProjectsByUser($search, $perPage, $status);
        $projects->load('owner');
        $projects->loadCount('tasks');
        return $projects;
    }

}
