<?php

use App\Http\Controllers\Api\FingerspotWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute API untuk integrasi sistem eksternal (termasuk webhook Fingerspot).
| Otomatis memiliki prefix '/api' dan bebas dari proteksi CSRF form session.
|
*/

Route::prefix('fingerspot')->group(function () {
    // Endpoint Webhook Realtime Fingerspot Developer Cloud
    Route::post('/webhook', [FingerspotWebhookController::class, 'handle'])->name('api.fingerspot.webhook');
});
