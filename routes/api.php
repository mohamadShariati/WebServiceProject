<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\V1\AuthController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\BrandController;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('/V1')->group(function(){

    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout']);


    Route::apiResource('/brand',(BrandController::class));
    Route::get('/brand/{brand}/brands-products',[BrandController::class,'products']);
    Route::get('/brand/{brand}/products',[BrandController::class,'productsBrands']);


    Route::apiResource('/category',(CategoryController::class));

    Route::get('/category/{category}/children',[CategoryController::class,'children']);
    Route::get('/category/{category}/parent',[CategoryController::class,'parent']);
    Route::get('/category/{category}/product',[CategoryController::class,'products']);
    Route::get('/category/{category}/category-products',[CategoryController::class,'CategoryProducts']);

    Route::apiResource('/product',(ProductController::class));
    Route::get('/product/{product}/category',[ProductController::class,'category']);

    Route::post('/payment/send',[PaymentController::class,'send']);
    Route::post('/payment/verify',[PaymentController::class,'verify']);

});

