<?php

namespace App\Http\Controllers;

use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct() {
        $this->middleware('roleUser:Buyer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json($transactions, 200);
    }

    public function getOwnTransaction(User $user)
    {
        $transactions = Transaction::where('user_id', $user->id)->get();
        return response()->json($transactions, 200);
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
        $stock = Product::find($request->product_id)->stock;
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric|min:1|max:'.$stock,
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $product = Product::find($request->product_id);
        $updateStock = Product::where('id', $request->product_id)->first()
            ->update([
                'stock' => ($product->stock - $request->quantity),
            ]);

        $isStored = Transaction::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_bill' => ($product->price * $request->quantity),
            'date' => Carbon::now(),
        ]);

        if($isStored)
            return response()->json([
                'success' => true,
                'message' => 'Transaction successfully!',
                'transaction' => $isStored,
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed!',
                'transaction' => $isStored,
            ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
