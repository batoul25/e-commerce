<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $product = ProductResource::collection(Product::get());
        return $this->successResponse($product);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        //
       $product = Product::create([
            'name' => $request -> name ,
            'description' => $request->description ,
            'price' => $request -> price ,
            'quantity' => $request -> quantity ,
        ]);

        return (new ProductResource ($product))->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try{
        $product = Product::findOrFail($id);

         return $this->successResponse(new ProductResource($product));

        }catch(\Exception $exception){
             return $this->errorResponse('The Product Is Not Found' , 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        //
        try{
           $product = Product::findOrFail($id);
           $product -> name = $request -> name;
           $product -> description = $request -> description;
           $product -> price = $request -> price;
           $product -> quantity = $request -> quantity;

           $product -> update();
           return $this->successResponse(new ProductResource($product) , 'The Product Updated Successfully' , 201);
        }catch(\Exception $exception){
            return $this->errorResponse('The Product Is Not Found' , 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try{
            $product = Product::findOrFail($id);
            $product->delete();
            return $this->successResponse(null , 'The Product Deleted Successfully' , 200);
        }catch(\Exception $exception){
            return $this->errorResponse('The Product Is Not Found' , 404);
        }

    }
}
