<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    LoanController,
    LoanStatusController,
    RepaymentController
};

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);    
    });
    Route::group(['prefix' => 'loan'], function () {
        Route::get('/get-all', [LoanController::class, 'getAllLoans']);
        Route::get('/summary/{loanRefId}', [LoanController::class, 'summary']);

        Route::group(['middleware' => ['role.customer']], function () {
            Route::post('/apply', [LoanController::class, 'apply']);
            Route::post('/repayment/{loanRefId}', [RepaymentController::class, 'repayment']);
        });
        Route::group(['middleware' => ['role.manager_or_admin']], function () {
            Route::post('/approve/{userId}/{loanRefId}', [LoanStatusController::class, 'approve']);
            Route::post('/reject/{userId}/{loanRefId}', [LoanStatusController::class, 'reject']);
        });
    });
});
