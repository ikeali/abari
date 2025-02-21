<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VirtualAccountController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/dynamic-virtual-account', [VirtualAccountController::class, 'createVirtualAccount']);
Route::post('/initiate-dynamic-virtual-account', [VirtualAccountController::class, 'initiateDynamicVirtualAccount']);
Route::get('/get-transactions/{transaction_reference}', [VirtualAccountController::class, 'getTransaction']);
Route::patch('/edit-transaction', [VirtualAccountController::class, 'editTransactionAmountDuration']);



Route::post('/simulate_payment', [VirtualAccountController::class, 'simulate_payment']);
Route::post('/payfixy_webhook', [WebhookController::class, 'handleWebhook']);

// Route::middleware('auth:sanctum')->post('/transactions', [TransactionController::class, 'initiateTransaction'])->name('transaction.create');

// Route::post('/webhook', function () {
//     include public_path('webhook.php');
// });





// Protected route (requires authentication)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/transactions', [TransactionController::class, 'initiateTransaction'])->name('transaction.create');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
