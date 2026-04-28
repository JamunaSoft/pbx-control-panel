<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// PBX API Routes (will be expanded in Phase 2-4)
Route::middleware(['auth:sanctum', 'rate.limit.api'])->group(function () {
    // Extensions
    Route::apiResource('extensions', App\Http\Controllers\Api\ExtensionController::class);

    // Trunks
    Route::apiResource('trunks', App\Http\Controllers\Api\TrunkController::class);

    // Queues
    Route::apiResource('queues', App\Http\Controllers\Api\QueueController::class);
    Route::post('queues/{queue}/extensions', [App\Http\Controllers\Api\QueueController::class, 'addExtension']);
    Route::delete('queues/{queue}/extensions/{extension}', [App\Http\Controllers\Api\QueueController::class, 'removeExtension']);
    Route::patch('queues/{queue}/extensions/{extension}/penalty', [App\Http\Controllers\Api\QueueController::class, 'updateExtensionPenalty']);

    // IVRs
    Route::apiResource('ivrs', App\Http\Controllers\Api\IvrController::class);

    // Call Routes
    Route::apiResource('call-routes', App\Http\Controllers\Api\CallRouteController::class);
    Route::post('call-routes/test', [App\Http\Controllers\Api\CallRouteController::class, 'test']);

    // CDR
    Route::get('cdr', [App\Http\Controllers\Api\CdrController::class, 'index']);
    Route::get('cdr/{cdr}', [App\Http\Controllers\Api\CdrController::class, 'show']);
    Route::get('cdr-stats', [App\Http\Controllers\Api\CdrController::class, 'stats']);
    Route::get('cdr-export', [App\Http\Controllers\Api\CdrController::class, 'export']);

    // Conference Rooms
    Route::apiResource('conference-rooms', App\Http\Controllers\Api\ConferenceRoomController::class);
    Route::get('conference-rooms-active', [App\Http\Controllers\Api\ConferenceRoomController::class, 'active']);
    Route::post('conference-rooms/{room}/kick', [App\Http\Controllers\Api\ConferenceRoomController::class, 'kickParticipant']);

    // Voicemails
    Route::apiResource('voicemails', App\Http\Controllers\Api\VoicemailController::class);
    Route::get('voicemails/{voicemail}/messages', [App\Http\Controllers\Api\VoicemailController::class, 'messages']);
    Route::delete('voicemails/{voicemail}/messages', [App\Http\Controllers\Api\VoicemailController::class, 'deleteMessage']);

    // Dashboard data
    Route::get('dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('dashboard/active-calls', [App\Http\Controllers\Api\DashboardController::class, 'activeCalls']);
    Route::get('dashboard/queue-status', [App\Http\Controllers\Api\DashboardController::class, 'queueStatus']);
    Route::get('dashboard/extension-status', [App\Http\Controllers\Api\DashboardController::class, 'extensionStatus']);
});