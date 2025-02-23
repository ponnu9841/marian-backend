<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PortfolioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//admin routes
Route::middleware('admin')->group(function () {

    //service
    Route::post('/service', [ServiceController::class, 'createService']);
    Route::put('/service', [ServiceController::class, 'updateService']);
    Route::delete('/service', [ServiceController::class, 'deleteService']);

    //portfolio
    Route::post('/portfolio', [PortfolioController::class, 'createPortfolio']);
    Route::put('/portfolio', [PortfolioController::class, 'updatePortfolio']);
    Route::delete('/portfolio', [PortfolioController::class, 'deletePortfolio']);

    //banner
    Route::post('/banner', [BannerController::class, 'createBanner']);
    Route::delete('/banner', [BannerController::class, 'deleteBanner']);

    //contact
    Route::post('/contact', [ContactController::class, 'createContact']);
    Route::put('/contact', [ContactController::class, 'updateContact']);

    //about
    Route::post('/about', [AboutController::class, 'createAbout']);
    Route::put('/about', [AboutController::class, 'updateAbout']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);

Route::get('/service', [ServiceController::class, 'getService']);
Route::get('/service/{id}', [ServiceController::class, 'getServiceDetail']);
Route::get('/portfolio', [PortfolioController::class, 'getPortfolio']);
Route::get('/banner', [BannerController::class, 'getBanner']);
Route::get('/contact', [ContactController::class, 'getContact']);
Route::get('/about', [AboutController::class, 'getAbout']);
