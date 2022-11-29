<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\CategoryResource;

class CategoryController extends ApiController
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category=Category::paginate(3);
        return $this->successResponse([
            'categories'=>CategoryResource::collection($category),
            'links'=>CategoryResource::collection($category)->response()->getData()->links,
            'meta'=>CategoryResource::collection($category)->response()->getData()->meta
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'parent_id'=>'numeric',
            'name'=>'required|string',
            'description'=>'required|string'
        ]);

        if ($validator->fails())
        {
            return $this->errorResponse($validator->messages(),422);
        }

        $category=Category::create([
            'parent_id'=>$request->parent_id,
            'name'=>$request->name,
            'description'=>$request->description
        ]);

        return $this->successResponse(new CategoryResource($category),201);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->successResponse(new CategoryResource($category),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Category $category)
    {
        $validator=Validator::make($request->all(),[
            'parent_id'=>'numeric',
            'name'=>'required|string',
            'description'=>'required|string',
        ]);

        if($validator->fails()){
            return $this->errorResponse($validator->messages(),422);
        }

        $category->update([
            'parent_id'=>$request->parent_id,
            'name'=>$request->name,
            'description'=>$request->description
        ]);

        return $this->successResponse(new CategoryResource($category),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->successResponse('category deleted successfully',200);
    }

    public function children(Category $category)
    {   
        return $this->successResponse(new CategoryResource($category->load('children')),200);
    }

    public function parent(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('parent')),200);
    }

    public function products(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('products')),200);
    }

    public function CategoryProducts(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('CategoryProducts')),200);
    }
}
