<?php

namespace App\Http\Controllers\Api\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\File;
use App\Http\Resources\FilesResource;
use Storage;

class FileController extends Controller
{
    public function upload($id, Request $request)
    {
        $data = $request->validate([
            'file'          => ['required', 'max:2048', 'mimes:jpeg,jpg,png,pdf'],
        ]);

        $task = Task::findOrFail($id);

        if(auth()->id() != $task->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        $fileName = $request->file('file')->hashName();

        $uploaded = $request->file('file')->storeAs('public/tasks/'.$task->id, $fileName);

        if($uploaded)
        {
            $file = $task->files()->create([
                'user_id'   => auth()->id(),
                'task_id'   => $task->user_id,
                'name'      => $fileName,
            ]);

            if($file)
            {
                return response()->json([
                    'message' => 'File uploaded successfully',
                    'file'    => new FilesResource($file),
                ]);
            }

        }

        return response()->json(['message' => 'Error occurred, please try again later!'], 500);
    }

    public function destroy(File $file)
    {
        if(auth()->id() != $file->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($file->delete())
        {
            $deleted = Storage::delete('public/tasks/'.$file->task_id.'/'.$file->name);
            if($deleted)
            {
                return response()->json(['message' => 'File deleted successfully']);
            }
        }

        return response()->json(['message' => 'Error occurred, please try again later!'], 500);

    }

}
