<?php

namespace App\Http\Controllers\V1;


use App\Models\Product;
use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\ProductResource;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return $this->successResponse(ProductResource::collection($products->load('images')), 200);
        // return $this->successResponse([
        //     'product'=>ProductResource::collection($products->load('images')),
        //     'links'=>ProductResource::collection($products)->response()->getData()->links,
        //     'meta'=>ProductResource::collection($products)->response()->getData()->meta
        // ],200);
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
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'numeric|exists:categories,id',
            'primary_image' => 'required|image',
            'price' => 'required',
            'quantity' => 'required',
            'delivery_amount' => 'required',
            'images.*' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }


        $imageName = Carbon::now()->microsecond . '.' . $request->primary_image->extension();
        $request->primary_image->storeAs('images/products', $imageName, 'public');

        if ($request->has('images')) {
            foreach ($request->images as $image) {

                $imageFileName = Carbon::now()->microsecond . '.' . $image->extension();
                $image->storeAs('images/products/oder', $imageFileName, 'public');
            }
        }


        $product = Product::create([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'primary_image' => $imageName,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'delivery_amount' => $request->delivery_amount
        ]);

        if ($request->has('images')) {
            foreach ($request->images as $imageFile) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageFileName
                ]);
            }
        }

        return $this->successResponse(new ProductResource($product), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product->load('images')), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'numeric|exists:categories,id',
            'primary_image' => 'nullable|image',
            'price' => 'required',
            'quantity' => 'required',
            'delivery_amount' => 'required',
            'images.*' => 'nullable|image'
        ]);


        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }


        if ($request->has('primary_image')) {
            $PrimaryimageName = Carbon::now()->microsecond . '.' . $request->primary_image->extension();
            $request->primary_image->storeAs('images/products', $PrimaryimageName, 'public');
            
        }
        

        if ($request->has('images')) {
            foreach ($request->images as $image) {
                $imageFileName = Carbon::now()->microsecond . '.' . $image->extension();
                $image->storeAs('images/product/oder', $imageFileName, 'public');
            }
        }

        $product->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'primary_image' => $request->has('primary_image') ? $PrimaryimageName : $product->primary_image,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'delivery_amount' => $request->delivery_amount
        ]);

        if ($request->has('images')) {

            foreach ($product->images as $imageProduct) {
                $imageProduct->delete();
            }

            foreach ($request->images as $imageFile) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageFileName
                ]);
            }
        }



        return $this->successResponse(new ProductResource($product), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->successResponse('product deleted successfully', 200);
    }

    public function category(Product $product)
    {
        return $this->successResponse(new CategoryResource($product->load('category')), 200);
    }

    
}
