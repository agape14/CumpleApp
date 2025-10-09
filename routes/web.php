<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamiliarController;
use App\Http\Controllers\IdeaRegaloController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas web de tu aplicación. Estas rutas
| son cargadas por el RouteServiceProvider y todas serán asignadas
| al grupo de middleware "web".
|
*/

// Ruta principal - Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rutas de recursos para familiares
Route::resource('familiares', FamiliarController::class)->parameters([
    'familiares' => 'familiar'
]);

// Rutas para ideas de regalos (anidadas dentro de familiares)
Route::post('familiares/{familiar}/ideas', [IdeaRegaloController::class, 'store'])
    ->name('familiares.ideas.store');

Route::put('ideas/{idea}', [IdeaRegaloController::class, 'update'])
    ->name('ideas.update');

Route::delete('ideas/{idea}', [IdeaRegaloController::class, 'destroy'])
    ->name('ideas.destroy');

