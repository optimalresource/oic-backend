<?php
use App\Http\Controllers\GigController;
use App\Http\Controllers\AuthController;

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

Route::group(['prefix' => 'v1'] , function(){
    Route::post('/gig', [GigController::class, "store"]);
    Route::put('/gig/{gig}', [GigController::class, "update"]);
    Route::get('/gig/{gig}', [GigController::class, "show"]);
    Route::delete('/gig/{gig}', [GigController::class, "destroy"]);
    Route::get('/gigs', [GigController::class, "index"]);

    Route::post('/auth/login', [AuthController::class, "login"])->name('login');
    Route::post('/auth/register', [AuthController::class, "register"]);
});

Route::any('{any}', function(){
    return response()->json([
        'status'    => false,
        'message'   => 'Page Not Found.',
    ], 404);
})->where('any', '.*');