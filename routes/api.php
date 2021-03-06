<?php

use App\Http\Controllers;
use App\Http\Controllers\AchievementsChaptersController;
use App\Http\Controllers\AchievementsMGQController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SchoolsController;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth;

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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['App\Http\Middleware\Cors', 'App\Http\Middleware\ForceJsonResponse']], function () {
    // ...

    //public data routes
    Route::get('/schools', [SchoolsController::class, 'get']);

    //public auth routes
    Route::post('/login', [Auth\ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/register', [Auth\ApiAuthController::class, 'register'])->name('register.api');
    Route::post('/resetPassword', [PasswordResetController::class, 'sendMail'])->name('resetPassword');

});

Route::middleware('auth:api')->group(function (){
    // our routes to be protected will go in here
    Route::post('/changePassword', [Controllers\UsersController::class, 'changePassword'])->name('changePassword');
    Route::post('/logout', [Auth\ApiAuthController::class, 'logout'])->name('logout.api');
    Route::post('/getFullUserProfile', [Controllers\UsersController::class, 'getFullProfile']);
    Route::post('/chapter', [AchievementsChaptersController::class, 'newEntry']);
    Route::post('/mgq', [AchievementsMGQController::class, 'newEntry']);


    Route::middleware('api.admin')->group(function(){
        // routes to be accessed ONLY by Admins (and SuperAdmins) will go in here
        Route::get('/users', [\App\Http\Controllers\UsersController::class, 'getAll']);
        Route::post('/user/getChapters', [AchievementsChaptersController::class, 'getChaptersAdvancement']);
        Route::post('/user/getMGQ', [AchievementsMGQController::class, 'getMGQAdvancement']);
    });

    Route::middleware('api.superAdmin')->group(function(){
        // routes to be accessed by SuperAdmins ONLY will go in here
        Route::post('/updateUser', [\App\Http\Controllers\UsersController::class, 'updateRights']);
        Route::post('/schools', [\App\Http\Controllers\SchoolsController::class, 'store']);
        Route::post('/deleteUser', [\App\Http\Controllers\UsersController::class, 'deleteUser']);
    });



});
