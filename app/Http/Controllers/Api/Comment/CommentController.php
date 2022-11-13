<?php

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Task;
use App\models\Comment;

class CommentController extends Controller
{
    public function __construct()
    {   // Limit the requests (comments) to ten requests only for store/update per minute to prevent attacks.
        $this->middleware(['throttle:10,1'])->except('destroy');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id' => ['required', 'numeric'],
            'content' => ['required', 'min:1'],
        ]);

        $request['user_id'] = auth()->id();
        $task = Task::findOrFail($request->task_id);

        if(auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        $comment = $task->comments()->create($request->all());

        if($comment)
        {
            return response()->json([
                'message' => 'Comment added successfully',
                'comment' => $comment,
            ]);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

    public function update(Comment $comment, Request $request)
    {
        $data = $request->validate([
            'content' => ['required', 'min:1'],
        ]);

        if(auth()->id() != $comment->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($comment->update(['content' => $data['content']]))
        {
            return response()->json(['message' => 'Comment updated successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

    public function destroy(Comment $comment)
    {
        if(auth()->id() != $comment->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($comment->delete())
        {
            return response()->json(['message' => 'Comment deleted successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

}
