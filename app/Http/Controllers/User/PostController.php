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
        $posts = Post::orderBy('id', 'DESC')->paginate(6);
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
}
