<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CooperativaController;

Route::get('/ping', function() {
    return ['pong'=>true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/user', [AuthController::class, 'create']);
Route::get('/random', [CooperativaController::class, 'createRandom']);

Route::middleware('auth:api')->group(function() {
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::get('/user', [UserController::class, 'read']);
Route::put('/user', [UserController::class, 'update']);
Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
Route::get('/user/favorites', [UserController::class, 'getFavorites']);
Route::post('/user/favorite', [UserController::class, 'toggleFavorite']);
Route::get('/user/appointments', [UserController::class, 'getAppointments']);

Route::get('/cooperativas', [CooperativaController::class, 'list']);
Route::get('/cooperativa/{id}', [CooperativaController::class, 'one']);
Route::post('/cooperativa/{id}/appointment', [CooperativaController::class, 'setAppointment']);

Route::get('/search', [CooperativaController::class, 'search']);

});