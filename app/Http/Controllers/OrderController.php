<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user
        ->orders()
        ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('product_id', 'qty', 'price');
        $validator = Validator::make($data, [
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new product
        $order = $this->user->orders()->create([
            'product_id' => $request->product_id,
            'qty' => $request->qty,
            'price' => $request->price
        ]);

        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = $this->user->orders()->find($id);
    
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, order not found.'
            ], 400);
        }
    
        return $order;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
          //Validate data
          $data = $request->only('product_id', 'qty', 'price');
          $validator = Validator::make($data, [
              'product_id' => 'required',
              'qty' => 'required',
              'price' => 'required'
          ]);
  
          //Send failed response if request is not valid
          if ($validator->fails()) {
              return response()->json(['error' => $validator->messages()], 200);
          }
  
          //Request is valid, update product
          $order = $order->update([
            'product_id' => $request->product_id,
            'qty' => $request->qty,
            'price' => $request->price
          ]);
  
          //Product updated, return success response
          return response()->json([
              'success' => true,
              'message' => 'Order updated successfully',
              'data' => $order
          ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ], Response::HTTP_OK);
    }
}
