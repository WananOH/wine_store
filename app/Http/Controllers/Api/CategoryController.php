<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller{
    public function index()
    {
        $category = Category::where('status',1)->orderBy('sort','desc')->get();

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $category]);
    }
}
