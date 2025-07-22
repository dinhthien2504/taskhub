<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\AssignUsersToProjectRequest;
use App\Http\Resources\Projects\ProjectResource;
use App\Http\Resources\Projects\ProjectUserResource;
use App\Services\Projects\ProjectUserService;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectUserController extends Controller
{
    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
    }

    public function index(Project $project)
    {
        try {
            $users = $this->projectUserService->getUsers($project->id);
            return ProjectUserResource::collection($users)->resolve();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy thông tin người dùng trong dự án thất bại.',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function store(AssignUsersToProjectRequest $request, Project $project)
    {
        try {
            $this->projectUserService->assignUsers($project->id, $request->user_ids);
            return response()->json(['message' => 'Người dùng đã được gán'], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project, User $user)
    {
        try {
            $this->projectUserService->removeUser($project->id, $user->id);
            return response()->json(['message' => 'Người dùng đã bị xoá khỏi dự án']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Xóa người dùng khỏi dự án thât bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getProjectByUser(Request $request)
    {
        try {  
            $projects = $this->projectUserService->getProjectsByUser($request);
            return ProjectResource::collection($projects);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy dự án của người dùng thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}