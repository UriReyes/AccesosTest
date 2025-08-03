<?php

use App\Http\Controllers\ActivoController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\VisitaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('visitas', VisitaController::class)->only(['index', 'create', 'store']);
    Route::get('visitas/externas/create', [VisitaController::class, 'externasCreate'])->name('visitas.externas.create');
    Route::post('visitas/externas/store', [VisitaController::class, 'externasStore'])->name('visitas.externas.store');

    Route::get('autorizar/visitas/', [VisitaController::class, 'autorizarIndex'])->name('autorizar.visitas.index');
    Route::get('/api/visitas/pendientes/', [VisitaController::class, 'pendientesJson'])->name('api.visitas.pendientes');

    Route::post('autorizar/visitas', [VisitaController::class, 'autorizar'])->name('autorizar.visitas');
    Route::get('escanear/visitas/qr-scan/index', [VisitaController::class, 'qrScanIndex'])->name('escanear.visitas.qr-scan.index');
    Route::post('escanear/visitas/qr-scan', [VisitaController::class, 'qrScan'])->name('escanear.visitas.qr-scan');
    Route::resource('sedes', SedeController::class)->only(['index', 'create', 'store']);
    Route::resource('activos', ActivoController::class)->only(['index', 'create', 'store']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
