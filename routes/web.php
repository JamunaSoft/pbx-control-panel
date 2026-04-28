<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard');

    // Extensions
    Route::resource('extensions', App\Http\Controllers\Web\ExtensionController::class);

    // Trunks
    Route::resource('trunks', App\Http\Controllers\Web\TrunkController::class);

    // Queues
    Route::resource('queues', App\Http\Controllers\Web\QueueController::class);
    Route::post('queues/{queue}/extensions', [App\Http\Controllers\Web\QueueController::class, 'addExtension'])->name('queues.add-extension');
    Route::delete('queues/{queue}/extensions/{extension}', [App\Http\Controllers\Web\QueueController::class, 'removeExtension'])->name('queues.remove-extension');

    // IVRs
    Route::resource('ivrs', App\Http\Controllers\Web\IvrController::class);

    // Call Routes
    Route::resource('call-routes', App\Http\Controllers\Web\CallRouteController::class);

    // CDR
    Route::get('cdr', [App\Http\Controllers\Web\CdrController::class, 'index'])->name('cdr.index');
    Route::get('cdr/export', [App\Http\Controllers\Web\CdrController::class, 'export'])->name('cdr.export');

    // Conference Rooms
    Route::resource('conference-rooms', App\Http\Controllers\Web\ConferenceRoomController::class);

    // Voicemails
    Route::resource('voicemails', App\Http\Controllers\Web\VoicemailController::class);

    // System Administration
    Route::get('system/status', [App\Http\Controllers\Web\SystemController::class, 'status'])->name('system.status');
    Route::get('audit-logs', [App\Http\Controllers\Web\AuditLogController::class, 'index'])->name('audit-logs.index');
});

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
