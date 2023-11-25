<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::all();
        return response()->json($blogs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,png,webp|max:2048', // 2MB Max
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $path = $request->file('image')->store('public/blogs');

        $blog = new Blog;
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->image = Storage::url($path);
        $blog->save();

        return response()->json($blog, 201);
    }

    // by id
    public function show(Blog $blog)
    {
        return response()->json($blog);
    }

    // Update
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpg,png,webp|max:2048', // 2MB Max
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('title')) {
            $blog->title = $request->title;
        }

        if ($request->has('description')) {
            $blog->description = $request->description;
        }

        if ($request->hasFile('image')) {
            if ($blog->image) {
                $oldImagePath = str_replace(asset('/'), '', $blog->image);
                Storage::delete($oldImagePath);
            }

            $path = $request->file('image')->store('public/blogs');
            $blog->image = Storage::url($path);
        }

        $blog->save();

        return response()->json($blog);
    }

    // Remove
    public function destroy($blog)
    {
        $blog = Blog::find($blog);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        try {
            if ($blog->image) {
                $oldImagePath = parse_url($blog->image);
                Storage::delete($oldImagePath);
            }

            $blog->delete();

            return response()->json(['message' => 'Blog deleted successfully'], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Failed to delete blog', 'error' => $exception->getMessage()], 500);
        }
    }
}
