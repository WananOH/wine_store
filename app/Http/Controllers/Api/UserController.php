<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;

class UserController extends Controller{

    public function index()
    {
        $category = Product::filter()->orderBy('sort','desc')->paginate();

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $category]);
    }


}
