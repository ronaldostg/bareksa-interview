<?php

use App\Http\Controllers\NewsController;
use App\Http\Controllers\TagsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

// Redis

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

// Route::group()
// Route::group([
//     'middleware'=>'api',
//     'prefix'=>'auth'    
// ],function () {
//     Route::get('/list-news', [NewsController::class, 'index']);
    
// });

Route::resource('news', NewsController::class);
Route::resource('tags', TagsController::class);


// Route::get('redis', function () {
//     $p = Redis::incr('p');
//     return $p;
//     // print_r($p);
// });
// Route::post('add-news', [NewsController::class, 'store']);

Route::get('testing', [NewsController::class, 'testing']);
Route::get('cache-file', [NewsController::class, 'latihan_cache']);

