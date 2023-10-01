<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = User::query()->where('remember_token', '=', $request->bearerToken())->first();

        if(!$user)
        {
            return response()->json(['message' => 'Login failed'], 403);
        }

        $cart = Cart::query()->where('user_id', '=', $user->id)->get();  

        return response()->json($cart, 200);
    }

    public function store(Request $request, $product_id)
    {
        $product = Product::query()->find($product_id);
        $user = User::query()->where('remember_token', '=', $request->bearerToken())->first();

        if(!$user)
        {
            return response()->json(['message' => 'Login failed'], 403);
        }

        if($product)
        {
            $cart = new Cart();

            $cart->user_id = $user->id;
            $cart->product_id = $product_id->id;

            $cart->save();

            return response()->json(['message' => 'Product add to cart'], 201);
        }

        else 
        {
            return response()->json(['message' => 'no such product'], 404);
        }
    }

    public function delete(Request $request, $product_id)
    {
        $product = Product::query()->find($product_id);

        if(!$product_id)
        {
            return response()->json(['message' => 'Product not founded'], 404);
        }

        $user = User::query()->where('remember_token', '=', $request->bearerToken())->first();

        if(!$user)
        {
            return response()->json(['message' => 'Login failed'], 403);
        }

        $product_in_cart = Cart::query()->where('user_id', $user->id)->where('product_id', $product_id)->first();

        if($product_in_cart)
        {
            $product_in_cart->delete();
            return response()->json(['message' => 'Product removed from cart'], 200);
        }

        else
        {
            return response()->json(['message' => 'Product not founded'], 404);
        }
    }

    public function order(Request $request)
    {
        $user = User::query()->where('remember_token', '=', $request->bearerToken())->first();

        if(!$user)
        {
            return response()->json(['message' => 'Login failed'], 403);
        }

        $products_in_cart = Cart::query()->where('user_id', '=', $user->id)->get();

        if($products_in_cart->isEmpty())
        {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        foreach($products_in_cart as $product)
        {
            $product->delete();
        }

        return response()->json(['message' => 'Order is processed'], 201);
    }
}
