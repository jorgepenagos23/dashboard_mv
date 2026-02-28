<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExplorerController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/tablas/{table?}', [ExplorerController::class, 'index'])->name('explorer.index');
    Route::post('/tablas/{table}/update', [ExplorerController::class, 'update'])->name('explorer.update');
    
    Route::resource('promociones', App\Http\Controllers\PromocionController::class);
    Route::resource('productos-promociones', App\Http\Controllers\ProductoPromocionController::class)->except(['create', 'show', 'edit', 'index']);
    
    Route::get('/notificaciones', [App\Http\Controllers\NotificationController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/enviar', [App\Http\Controllers\NotificationController::class, 'send'])->name('notificaciones.send');
    
        Route::get('/pedidos', [App\Http\Controllers\OrderController::class, 'index'])->name('pedidos.index');
        Route::post('/pedidos/sistema', [App\Http\Controllers\OrderController::class, 'uploadSystem'])->name('pedidos.upload_system');

        Route::get('/clientes', [App\Http\Controllers\CustomerController::class, 'index'])->name('clientes.index');
        Route::get('/inventario', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventario.index');
        Route::post('/inventario/add-stock', [App\Http\Controllers\InventoryController::class, 'addStock'])->name('inventario.add_stock');
    });

    require __DIR__.'/auth.php';
