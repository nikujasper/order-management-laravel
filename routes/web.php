<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\orderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/orderFormAction', [orderController::class, 'orderfun']);
Route::get('/getproducturl', [orderController::class, 'getProductFun']);
Route::get('/getOrderDetail', [orderController::class, 'getOrderDetail']);
Route::get('/deleteOrder', [orderController::class, 'deleteOrder']);
Route::get('/', [orderController::class, 'orderList']);
