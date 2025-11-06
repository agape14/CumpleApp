<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamiliarController;
use App\Http\Controllers\IdeaRegaloController;
use App\Http\Controllers\AuthController;
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

// Rutas de autenticación (sin middleware)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['familiar.auth'])->group(function () {

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

// Rutas para relaciones familiares
Route::post('relaciones-familiares', [App\Http\Controllers\RelacionFamiliarController::class, 'store'])
    ->name('relaciones.store');

Route::delete('relaciones-familiares/{relacion}', [App\Http\Controllers\RelacionFamiliarController::class, 'destroy'])
    ->name('relaciones.destroy');

Route::get('familiares/{familiar}/relaciones', [App\Http\Controllers\RelacionFamiliarController::class, 'obtenerRelaciones'])
    ->name('relaciones.obtener');

// Rutas para regalos dados (historial)
Route::get('familiares/{familiar}/regalos-dados', [App\Http\Controllers\RegaloDadoController::class, 'index'])
    ->name('regalos-dados.index');

Route::post('familiares/{familiar}/regalos-dados', [App\Http\Controllers\RegaloDadoController::class, 'store'])
    ->name('regalos-dados.store');

Route::put('regalos-dados/{regalo}', [App\Http\Controllers\RegaloDadoController::class, 'update'])
    ->name('regalos-dados.update');

Route::delete('regalos-dados/{regalo}', [App\Http\Controllers\RegaloDadoController::class, 'destroy'])
    ->name('regalos-dados.destroy');

Route::get('familiares/{familiar}/regalos-dados/estadisticas', [App\Http\Controllers\RegaloDadoController::class, 'estadisticas'])
    ->name('regalos-dados.estadisticas');

// Rutas para recordatorios personalizados
Route::post('familiares/{familiar}/recordatorios', [App\Http\Controllers\RecordatorioController::class, 'store'])
    ->name('recordatorios.store');

Route::put('recordatorios/{recordatorio}', [App\Http\Controllers\RecordatorioController::class, 'update'])
    ->name('recordatorios.update');

Route::delete('recordatorios/{recordatorio}', [App\Http\Controllers\RecordatorioController::class, 'destroy'])
    ->name('recordatorios.destroy');

Route::post('recordatorios/{recordatorio}/toggle', [App\Http\Controllers\RecordatorioController::class, 'toggleActivo'])
    ->name('recordatorios.toggle');

Route::get('familiares/{familiar}/recordatorios', [App\Http\Controllers\RecordatorioController::class, 'obtenerPorFamiliar'])
    ->name('recordatorios.obtener');

// Rutas para árbol genealógico
Route::get('arbol-genealogico', [App\Http\Controllers\ArbolGenealogicoController::class, 'index'])
    ->name('arbol-genealogico.index');

Route::get('arbol-genealogico/generar/{familiar?}', [App\Http\Controllers\ArbolGenealogicoController::class, 'generarArbol'])
    ->name('arbol-genealogico.generar');

Route::get('arbol-genealogico/completo', [App\Http\Controllers\ArbolGenealogicoController::class, 'generarArbolCompleto'])
    ->name('arbol-genealogico.completo');

Route::get('arbol-genealogico/{familiar}/descendientes', [App\Http\Controllers\ArbolGenealogicoController::class, 'obtenerDescendientes'])
    ->name('arbol-genealogico.descendientes');

// Rutas para Google Calendar
Route::get('google-calendar/exportar/{familiar}', [App\Http\Controllers\GoogleCalendarController::class, 'exportar'])
    ->name('google-calendar.exportar');

Route::get('google-calendar/exportar-todos', [App\Http\Controllers\GoogleCalendarController::class, 'exportarTodos'])
    ->name('google-calendar.exportar-todos');

Route::post('google-calendar/generar-ics', [App\Http\Controllers\GoogleCalendarController::class, 'generarICS'])
    ->name('google-calendar.generar-ics');

// Rutas para WhatsApp
Route::post('whatsapp/enviar/{familiar}', [App\Http\Controllers\WhatsAppController::class, 'enviarNotificacion'])
    ->name('whatsapp.enviar');

Route::post('whatsapp/enviar-recordatorios', [App\Http\Controllers\WhatsAppController::class, 'enviarRecordatorios'])
    ->name('whatsapp.enviar-recordatorios');

Route::post('whatsapp/probar', [App\Http\Controllers\WhatsAppController::class, 'probarConfiguracion'])
    ->name('whatsapp.probar');

// Rutas para configuración
Route::get('configuracion', [App\Http\Controllers\ConfiguracionController::class, 'index'])
    ->name('configuracion.index');

Route::post('configuracion/actualizar', [App\Http\Controllers\ConfiguracionController::class, 'actualizar'])
    ->name('configuracion.actualizar');

Route::post('configuracion/actualizar-multiples', [App\Http\Controllers\ConfiguracionController::class, 'actualizarMultiples'])
    ->name('configuracion.actualizar-multiples');

Route::post('configuracion/tema', [App\Http\Controllers\ConfiguracionController::class, 'actualizarTema'])
    ->name('configuracion.tema');

Route::post('configuracion/google-calendar', [App\Http\Controllers\ConfiguracionController::class, 'actualizarGoogleCalendar'])
    ->name('configuracion.google-calendar');

Route::post('configuracion/whatsapp', [App\Http\Controllers\ConfiguracionController::class, 'actualizarWhatsApp'])
    ->name('configuracion.whatsapp');

Route::get('configuracion/obtener-todas', [App\Http\Controllers\ConfiguracionController::class, 'obtenerTodas'])
    ->name('configuracion.obtener-todas');

Route::get('configuracion/obtener/{clave}', [App\Http\Controllers\ConfiguracionController::class, 'obtener'])
    ->name('configuracion.obtener');

Route::post('configuracion/restablecer', [App\Http\Controllers\ConfiguracionController::class, 'restablecerDefecto'])
    ->name('configuracion.restablecer');

// API para obtener familiares (para dropdowns)
Route::get('api/familiares', [App\Http\Controllers\FamiliarController::class, 'apiIndex'])
    ->name('api.familiares');

// Rutas para cuotas mensuales
Route::get('cuotas-mensuales', [App\Http\Controllers\CuotaMensualController::class, 'index'])
    ->name('cuotas-mensuales.index');

Route::get('cuotas-mensuales/historial', [App\Http\Controllers\CuotaMensualController::class, 'historial'])
    ->name('cuotas-mensuales.historial');

Route::post('cuotas-mensuales', [App\Http\Controllers\CuotaMensualController::class, 'store'])
    ->name('cuotas-mensuales.store');

Route::put('cuotas-mensuales/{cuota}', [App\Http\Controllers\CuotaMensualController::class, 'update'])
    ->name('cuotas-mensuales.update');

Route::delete('cuotas-mensuales/{cuota}', [App\Http\Controllers\CuotaMensualController::class, 'destroy'])
    ->name('cuotas-mensuales.destroy');

Route::post('cuotas-mensuales/{cuota}/marcar-pagada', [App\Http\Controllers\CuotaMensualController::class, 'marcarPagada'])
    ->name('cuotas-mensuales.marcar-pagada');

Route::post('cuotas-mensuales/generar-mes', [App\Http\Controllers\CuotaMensualController::class, 'generarCuotasMes'])
    ->name('cuotas-mensuales.generar-mes');

Route::get('cuotas-mensuales/estadisticas', [App\Http\Controllers\CuotaMensualController::class, 'estadisticas'])
    ->name('cuotas-mensuales.estadisticas');

Route::get('cuotas-mensuales/colectas-especiales', [App\Http\Controllers\CuotaMensualController::class, 'colectasEspeciales'])
    ->name('cuotas-mensuales.colectas-especiales');

Route::post('cuotas-mensuales/crear-colecta', [App\Http\Controllers\CuotaMensualController::class, 'crearColecta'])
    ->name('cuotas-mensuales.crear-colecta');

Route::post('cuotas-mensuales/registrar-aporte', [App\Http\Controllers\CuotaMensualController::class, 'registrarAporte'])
    ->name('cuotas-mensuales.registrar-aporte');

}); // Fin del grupo de rutas protegidas

