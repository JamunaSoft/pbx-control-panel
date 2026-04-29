<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\AuditLogController;
use App\Http\Controllers\Web\CallRouteController;
use App\Http\Controllers\Web\CdrController;
use App\Http\Controllers\Web\ConferenceRoomController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ExtensionController;
use App\Http\Controllers\Web\IvrController;
use App\Http\Controllers\Web\QueueController;
use App\Http\Controllers\Web\SystemController;
use App\Http\Controllers\Web\TrunkController;
use App\Http\Controllers\Web\VoicemailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Extensions
    Route::resource('extensions', ExtensionController::class);

    // Trunks
    Route::resource('trunks', TrunkController::class);

    // Queues
    Route::resource('queues', QueueController::class);
    Route::post('queues/{queue}/extensions', [QueueController::class, 'addExtension'])->name('queues.add-extension');
    Route::delete('queues/{queue}/extensions/{extension}', [QueueController::class, 'removeExtension'])->name('queues.remove-extension');

    // IVRs
    Route::resource('ivrs', IvrController::class);

    // Call Routes
    Route::resource('call-routes', CallRouteController::class);

    // CDR
    Route::get('cdr', [CdrController::class, 'index'])->name('cdr.index');
    Route::get('cdr/export', [CdrController::class, 'export'])->name('cdr.export');

    // Conference Rooms
    Route::resource('conference-rooms', ConferenceRoomController::class);

    // Voicemails
    Route::resource('voicemails', VoicemailController::class);

    // System Administration
    Route::get('system/status', [SystemController::class, 'status'])->name('system.status');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
