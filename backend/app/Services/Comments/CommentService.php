<?php

namespace App\Services\Comments;

class CommentService
{
    public function createComment($request, $task)
    {
        $data = [
            'user_id' => auth()->id(),
            'content' => $request->content,
            'created_at' => now(),
        ];
        $comment = $task->comments()->create($data);
        $comment->load('user');
        return $comment;
    }
}