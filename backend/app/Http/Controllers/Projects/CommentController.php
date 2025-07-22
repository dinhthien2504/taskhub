<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\StoreCommentRequest;
use App\Http\Resources\Comments\CommentResource;
use App\Models\Task;
use App\Services\Comments\CommentService;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(StoreCommentRequest $request, Task $task)
    {
        try {
            $comment = $this->commentService->createComment($request, $task);
            return new CommentResource($comment);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thêm comment thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}