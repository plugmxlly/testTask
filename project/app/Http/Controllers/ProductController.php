<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();

        return response()->json($products, 200);
    }

    public function store(Request $request) {

        $rules = array(
            'name' => ['required', 'string', 'min:1', 'max:50'],
            'description' => ['required', 'string', 'min:1', 'max:200'],
            'cost' => ['required', 'int'],
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(['message' => 'Validation error'], 422);
        }

        $validated = $validator->validated();

        $product = new Product();

        $product->name = $validated['name'];
        $product->description = $validated['description'];
        $product->cost = $validated['cost'];

        $product->save();

        return response()->json(['message' => 'Product added'], 201);
    }

    public function delete($product_id){

        $product = Product::query()->find($product_id);

        if ($product){
            $product->delete();
            return response()->json(['message' => 'Item removed from cart'], 200);
        }
        else{
            return response()->json(['message' => 'Item not found'], 404);
        }
    }

    public function edit(Request $request, $product_id){

        $product = Product::query()->find($product_id);

        if(!$product){
            return response()->json(['message' => 'Item not found'], 404);
        }

        $name = Validator::make($request->all(), ['name' => 'required', 'string', 'min:1', 'max:50']);
        if(!$name->fails()){
            $product->update(['name' => $name->validated()['name']]);
        }

        $description = Validator::make($request->all(), ['description' => 'required', 'string', 'min:1', 'max:200']);
        if(!$description->fails()){
            $product->update(['description' => $description->validated()['description']]);
        }

        $cost = Validator::make($request->all(), ['cost' => 'required', 'int']);
        if(!$cost->fails()){
            $product->update(['cost' => $cost->validated()['cost']]);
        }

        if ($name->fails() && $description->fails() && $cost->fails()){
            return response()->json(['message' => 'Validation error'], 422);
        }

        return response()->json($product, 200);
    }
}