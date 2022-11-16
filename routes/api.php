<?php

use App\Http\Controllers\V1\BrandController;
use App\Http\Controllers\V1\CategoryController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::apiResource('/brand',(BrandController::class));
    Route::apiResource('/category',(CategoryController::class));

    Route::get('/category/{category}/children',[CategoryController::class,'children']);
    Route::get('/category/{category}/parent',[CategoryController::class,'parent']);
});

