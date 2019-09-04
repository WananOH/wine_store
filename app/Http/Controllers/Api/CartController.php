<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RemoveCartRequest;
use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Services\CartService;

class CartController extends Controller
{
    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * CartController constructor.
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $cartItems = $this->cartService->get();

        return response()->json(['status_code' => 200,'message' => '获取成功','data' => $cartItems]);
    }

    /**
     * Add products to cart.
     *
     * @param  \App\Http\Requests\AddCartRequest  $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('product_id'), $request->input('amount'));

        return response()->json(['status_code' => 201,'message' => '添加成功']);
    }

    /**
     * @param RemoveCartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(RemoveCartRequest $request)
    {
        $this->cartService->remove($request->get('product_id'));

        return response()->json(['status_code' => 201,'message' => '删除成功']);
    }
}
