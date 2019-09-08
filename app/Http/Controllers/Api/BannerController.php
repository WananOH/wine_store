<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use App\Http\Controllers\Controller;

class BannerController extends Controller{
    public function index()
    {
        $banner = Banner::where('status',1)->orderBy('sort','desc')->get();

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $banner]);
    }
}
