<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VideoController;
use App\Models\Experiment;

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES (NO AUTH)
|--------------------------------------------------------------------------
*/


Route::get('/cf-test', function () {
    return \App\Services\CloudFrontCookieSigner::sign('experiments/1', 5);
});

Route::get('/video/{experiment}',[VideoController::class,'getVideo']);
// ✅ TEST ROUTE (NO AUTH, NO WEB)
Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'time' => now()->toDateTimeString(),
        'user' => null,
    ]);
});

// ✅ LOGIN (Sanctum cookie-based login)
//Route::middleware('web')->group(function () {
//Route::post('/login', [AuthController::class, 'login']);
//});

/*
|--------------------------------------------------------------------------
| PROTECTED API ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', fn (Request $r) => $r->user());
    //Route::post('/logout', [AuthController::class, 'logout']);

    // ---------- EXPERIMENT LIST ----------
    Route::get('/experiments', function () {
        return Experiment::where('is_active', true)->get();
    });

    // ---------- EXPERIMENT DETAIL ----------
    Route::get('/experiments/{experiment}', function (Experiment $experiment) {
        return response()->json([
            'id'        => $experiment->id,
            'title'     => $experiment->title,
            'aim'       => $experiment->aim,
            'objective' => $experiment->objective,
            'procedure' => $experiment->procedure,
            'video_url' => $experiment->video_url,
        ]);
    });

    // ---------- VIDEO (SETS CLOUDFRONT COOKIES) ----------
//    Route::get('/video/{experiment}', [VideoController::class, 'getVideo']);
});

