<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct() {
        $this->middleware('roleUser:Buyer')->only(['show', 'index']);
        $this->middleware('roleUser:Seller')->except(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::all();
        if($product)
            return response()->json([
                'success' => true,
                'message' => 'Get data success',
                'data' => $product,
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'Get data failed',
                'data' => $healthAgencies,
            ], 200);
    }

    public function getOwnProduct(User $user)
    {
        $product = Product::where('user_id', $user->id)->get();
        return response()->json($product, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'stock' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $isStored = Product::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'stock' => $request->stock,
            'price' => $request->price,
        ]);

        if($isStored)
            return response()->json([
                'success' => true,
                'message' => 'Add product successfully!',
                'product' => $isStored,
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'Add product failed!',
                'product' => $isStored,
            ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json($product, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'addition' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $isUpdate = Product::where('id', $product->id)->first()
            ->update([
                'name' => $request->name,
                'stock' => ($request->addition + $product->stock),
                'price' => $request->price,
            ]);

        if($isUpdate)
            return response()->json([
                'success' => true,
                'message' => 'update product successfully!',
                'product' => $isUpdate,
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'update product failed!',
                'product' => $isUpdate,
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Delete product successfully!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Delete product failed!',
            ], 500);
        }
    }
}
