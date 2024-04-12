<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => '2']);
    }

    public function store(CategoryRequest $request)
    {
                

        return response()->json(['message' => $request->all()]);
    }
}
