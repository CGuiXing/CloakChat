<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PusherController;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Controllers\DashboardController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Routes that require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/test', [TestController::class, 'index'])->name('test');
    Route::post('/test/submit', [TestController::class, 'submit']);

    // Routes with 'personality.test' middleware
    Route::middleware(['personality.test'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

Route::middleware(['auth', 'personality.test'])->group(function () {
    Route::prefix('chat')->group(function () {
        Route::get('/', [PusherController::class, 'index'])->name('index');
        Route::post('/create-room', [PusherController::class, 'createRoom'])->name('createRoom');
        Route::post('/join-room', [PusherController::class, 'joinRoom'])->name('joinRoom');
        Route::get('/chat-room/{roomName}', [PusherController::class, 'chatRoom'])
            ->where('roomName', '[A-Za-z0-9]+')
            ->name('chatRoom');

        Route::post('/chat-room/{roomName}/broadcast', [PusherController::class, 'broadcast']);
        Route::post('/chat-room/{roomName}/receive', [PusherController::class, 'receive']);
        Route::get('/leave-room', [PusherController::class, 'leaveRoom'])->name('leaveRoom');
        Route::get('/delete-room/{roomName}', [PusherController::class, 'deleteRoom'])->name('deleteRoom');
    });

    Route::post('/updateUserStatistics', [PusherController::class, 'updateUserStatistics']);
});