<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search_keyword = $request->input('search');
        $search_category = $request->input('category');

        $posts = Post::orderBy('id', 'DESC');
        
        if ($search_category) {
            $posts->where('category_id', $search_category);
        } else {
            $posts->where('title', 'like', "%$search_keyword%");
        }

        $posts      = $posts->paginate(6);
        $categories = $this->getCategory();
        $unique_category_ids = Post::select('category_id')
            ->distinct()
            ->get();
        $latest_posts = Post::orderBy('id', 'DESC')
            ->select('id', 'title', 'slug', 'image', 'updated_at')
            ->limit(3)
            ->get();

        return response()->json([
            'posts'      => $posts,
            'categories' => $categories,
            'uniqueCategoryIds' => $unique_category_ids,
            'latestPosts' => $latest_posts
        ]);
    }

    public function getCategory()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function show(Request $request)
    {
        $slug = $request->slug;
        $post = Post::where('slug', $slug)->first();

        return response()->json([
            'post' => $post
        ]);
    }
}
