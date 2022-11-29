<?php

namespace App\Http\Controllers\V1;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\BrandResource;
use App\Http\Resources\V1\ProductResource;
use Illuminate\Support\Facades\Validator;

class BrandController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::paginate(2);
        return $this->successResponse([
            'brands'=> BrandResource::collection($brands),
            'links'=> BrandResource::collection($brands)->response()->getData()->links,
            'meta'=> BrandResource::collection($brands)->response()->getData()->meta
        ],200);
       
        // return $this->successResponse(new BrandResource($brands),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string',
            'display_name'=>'required|unique:brands'
        ]);

        if ($validator->fails())
        {
            return $this->errorResponse($validator->messages(),422);
        }

        $brand = Brand::create([
            'name'=>$request->name,
            'display_name'=>$request->display_name
        ]);

        return $this->successResponse(new BrandResource($brand),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        return $this->successResponse(new BrandResource($brand),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string',
            'display_name'=>'required|unique:brands'
        ]);

        if ($validator->fails())
        {
            return $this->errorResponse($validator->messages(),422);
        }

        $brand = Brand::where('id',$brand->id)->first();

        $brand->update([
            'name'=>$request->name,
            'display_name'=>$request->display_name
        ]);

        return $this->successResponse(new BrandResource($brand),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return $this->successResponse('Brand deleted successfully',200);
    }

    public function products(Brand $brand)
    {
        
        return $this->successResponse(new BrandResource($brand->load('products')),200);
    }

    public function productsBrands(Brand $brand)
    {
        $products=Product::where('brand_id',$brand->id)->get();
        return $this->successResponse(ProductResource::collection($products),200);
    }
}
