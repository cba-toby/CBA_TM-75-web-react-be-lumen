<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('id', 'DESC')->paginate(10);
        $categories = $this->getCategory();

        return response()->json([
            'posts'     => $posts,
            'categories'  => $categories
        ]);
    }

    public function getCategory()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function store(PostRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->category_id == '') {
                $data['category_id'] = null;
            }
            if ($request->hasFile('image')) {
                $file= $request->file('image');
                $ext= $file->getClientOriginalExtension();
                $filename=time().'image.'.$ext;
                $imagePath = $file->storeAs('images',$filename, 'public');
                $data['image'] = $filename;
            }
            $post = Post::create($data);

            return response(new PostResource($post), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }

    public function show($id)
    {
        $post = Post::find($id);
        if ($post) {
            $categories = $this->getCategory();
            $imagePath = route('post.get_image', ['filename' => $post->image]);

            return response()->json([
                'post'     => new PostResource($post),
                'categories'  => $categories,
                'image'       => $imagePath
            ]);
        }

        return response()->json([
            'message' => 'Post not found'
        ], 404);
    }

    public function update(PostRequest $request, $id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                $data = $request->validated();
                $exitTitle = Post::where('title', $request->title)->where('id', '!=', $id)->exists();
                $exitSlug = Post::where('slug', $request->slug)->where('id', '!=', $id)->exists();
                if($exitTitle || $exitSlug){
                    return response()->json([
                        'errors' => ['Title or Slug already exists in another category']
                    ], 421);
                }
                
                if($request->category_id == ''){
                    $data['category_id'] = null;
                }

                if ($request->hasFile('image')) {
                    $file= $request->file('image');
                    $ext= $file->getClientOriginalExtension();
                    $filename=time().'image.'.$ext;
                    $imagePath = $file->storeAs('images',$filename, 'public');
                    $data['image'] = $filename;
                }
                $post->update($data);
                
                return response()->json($data);
            }
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return response()->json([
                'message' => 'Post deleted successfully'
            ]);
        }
        return response()->json([
            'message' => 'Post not found'
        ], 404);
    }

    public function getImage()
    {
        $filename = request()->filename;
        $path = storage_path('app/public/images/' . $filename);
        if (!file_exists($path)) {
            return response()->json([
                'message' => 'Image not found'
            ], 404);
        }
        return response()->file($path);
    }
}