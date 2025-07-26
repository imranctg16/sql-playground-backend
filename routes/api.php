<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StreakController;

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

// Authentication routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/check-token', [AuthController::class, 'checkToken']);
});

// Questions routes (public)
Route::get('/questions', [QuestionController::class, 'index']);
Route::get('/questions/{id}', [QuestionController::class, 'show']);
Route::get('/questions/{id}/solution', [QuestionController::class, 'getSolution']);
Route::get('/questions/difficulty/{difficulty}', [QuestionController::class, 'getByDifficulty']);
Route::get('/categories', [QuestionController::class, 'getCategories']);

// Query evaluation (public but saves progress if authenticated)
Route::post('/evaluate', [QuestionController::class, 'evaluateQuery']);

// User progress (protected route)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-progress', [QuestionController::class, 'getUserProgress']);
});

// Streak and activity tracking (works for both authenticated and guest users)
Route::post('/activity/record', [StreakController::class, 'recordActivity']);
Route::post('/activity/attempt', [StreakController::class, 'incrementAttempt']);
Route::post('/activity/complete', [StreakController::class, 'incrementCompletion']);
Route::get('/streak', [StreakController::class, 'getStreak']);
Route::get('/activity/calendar', [StreakController::class, 'getActivityCalendar']);

// Progress management (protected route)
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/progress/reset', [StreakController::class, 'resetProgress']);
});

// Database schema and sample data
Route::get('/schema', [QuestionController::class, 'getTableSchema']);
Route::get('/sample-data', [QuestionController::class, 'getSampleData']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'SQL Playground API is running'
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
