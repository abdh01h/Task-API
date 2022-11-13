<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = auth()->user()->categories()->with(['tasks' => function($query) {
            $query->paginate(10);
        }])->paginate(10);
        // load/with will reduce the queries to two only one is for categories and the last one is for tasks

        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'min:1', 'max:255']
        ]);

        $category = auth()->user()->categories()->create($request->all());

        if($category)
        {
            return response()->json(['message'   => 'Category created successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

    public function show(Category $category)
    {
        if(auth()->id() != $category->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        $category->load(['tasks' => function($query) {
            $query->paginate(10);
        }]);
        
        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {

        if(auth()->id() != $category->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        $data = $request->validate([
            'title' => ['required', 'min:1', 'max:255']
        ]);

        if($category->update($request->all()))
        {
            return response()->json(['message' => 'Category updated successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }
    }

    public function destroy(Category $category)
    {
        if(auth()->id() != $category->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($category->delete())
        {
            return response()->json(['message' => 'Category deleted successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }

    public function forceDelete($id)
    {

        $category = Category::withTrashed()->findOrFail($id);

        if(auth()->id() != $category->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($category->forceDelete())
        {
            return response()->json(['message' => 'Category deleted permanently']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }

    public function restore($id)
    {

        $category = Category::onlyTrashed()->findOrFail($id);

        if(auth()->id() != $category->user_id)
        {
            return response()->json(['message' => 'Error: Unauthorized access'], 401);
        }

        if($category->restore())
        {
            return response()->json(['message' => 'Category restored successfully']);

        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }
}
