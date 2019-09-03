<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller{
    public function index()
    {
        $product = Product::select(['id','title','thumb','price'])->filter()->orderBy('sort','desc')->paginate(10);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $product]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $product]);
    }
}
