<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\Product;
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();
        return view('cart.index', compact('cartItems', 'addresses'));
    }

    /**
     * Add products to cart.
     *
     * @param  \App\Http\Requests\AddCartRequest  $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));
        return [];
    }

    /**
     * Remove products from cart.
     *
     * @param  \App\Models\ProductSku  $sku
     * @return array
     */
    public function remove(ProductSku $sku)
    {
        $this->cartService->remove($sku->id);
        return [];
    }
}
