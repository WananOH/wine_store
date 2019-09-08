<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Notice;

class NoticeController extends Controller{
    public function index()
    {
        $notice = Notice::where('status',1)->orderBy('sort','desc')->get();

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $notice]);
    }
}
