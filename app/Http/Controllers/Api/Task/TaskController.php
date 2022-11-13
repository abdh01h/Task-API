<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskAdded;

class TaskController extends Controller
{

    public function index()
    {
        $tasks = auth()->user()->tasks()->with('category')->paginate(10);
        return TaskResource::collection($tasks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'min:1', 'max:255'],
            'category_id'   => ['required', 'numeric'],
            'due_date'      => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:'.date('Y-m-d')],
        ]);

        $request['user_id'] = auth()->id();
        $category = Category::findOrFail($request->category_id);

        if(auth()->id() != $category->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        $task = $category->tasks()->create($request->all());

        if($task)
        {

            $task_id    = $task->id;
            $task_title = $task->title;
            $due_date   = $task->due_date;

            $send_to           = auth()->user();
            $send_to->notify(new TaskAdded($task_id , $task_title, $due_date));

            return response()->json([
                'message' => 'Task created successfully',
                'task'    => $task,
            ]);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

    public function show(Task $task)
    {
        if(auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        $task->load('category', 'comments', 'files');

        return new TaskResource($task);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title'         => ['required', 'min:1', 'max:255'],
            'category_id'   => ['required', 'numeric'],
            'due_date'      => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $category = Category::findOrFail($request->category_id);

        if(auth()->id() != $category->user_id || auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($task->update($request->all()))
        {
            return response()->json(['message' => 'Task updated successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }

    public function destroy(Task $task)
    {
        if(auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($task->delete())
        {
            return response()->json(['message' => 'Task deleted successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

    public function forceDelete($id)
    {

        $task = Task::withTrashed()->findOrFail($id);

        if(auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($task->forceDelete())
        {
            Storage::deleteDirectory('public/tasks/'.$task->id);
            return response()->json(['message' => 'Task deleted permanently']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }

    public function restore($id)
    {

        $task = Task::onlyTrashed()->findOrFail($id);

        if(auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($task->restore())
        {
            return response()->json(['message' => 'Task restored successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }

}
