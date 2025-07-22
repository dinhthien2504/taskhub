<?php

namespace App\Http\Controllers\Projects;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\Projects\ProjectResource;
use App\Services\Projects\ProjectService;
use Illuminate\Http\Request;

class ProjectController
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $perPage = $request->query('per_page', 10);
            $status = $request->query('status', 'in_progress');

            $projects = $this->projectService->getAllProjects($search, $perPage, $status);
            return ProjectResource::collection($projects);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy người dùng không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(StoreProjectRequest $request)
    {
        try {
            $project = $this->projectService->createProject($request);
            return new ProjectResource($project);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo dự án.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        try {
            $project = $this->projectService->updateProject($request, $id);
            return new ProjectResource($project);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật dự án.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            $this->projectService->updateProjectStatus($request, $id, );
            return response()->json([
                'message' => 'Cập nhật trạng thái thành công.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật dự án.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}