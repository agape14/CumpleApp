<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\FamiliarApiController;
use App\Http\Controllers\Api\FcmTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas API de tu aplicación. Estas rutas
| son cargadas por el RouteServiceProvider y todas serán asignadas
| al grupo de middleware "api".
|
*/

Route::prefix('v1')->group(function () {
    
    // Rutas públicas (sin autenticación)
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::get('/dashboard/public', [DashboardApiController::class, 'publicDashboard']);
    
    // FCM Token - Puede ser con o sin autenticación
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);
    Route::delete('/fcm-token', [FcmTokenController::class, 'destroy']);
    
    // Rutas protegidas (requieren autenticación con Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Autenticación
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
        
        // Dashboard
        Route::get('/dashboard', [DashboardApiController::class, 'index']);
        
        // Familiares
        Route::apiResource('familiares', FamiliarApiController::class);
        Route::get('/familiares/proximos-cumpleanos', [FamiliarApiController::class, 'proximosCumpleanos']);
        Route::get('/parentescos', [FamiliarApiController::class, 'parentescos']);
        
        // TODO: Agregar más rutas cuando se creen los controladores
        // Ideas de regalos
        // Route::post('familiares/{familiar}/ideas', [IdeaRegaloApiController::class, 'store']);
        // Route::put('ideas/{idea}', [IdeaRegaloApiController::class, 'update']);
        // Route::delete('ideas/{idea}', [IdeaRegaloApiController::class, 'destroy']);
        
        // Relaciones familiares
        // Route::get('familiares/{familiar}/relaciones', [RelacionFamiliarApiController::class, 'index']);
        // Route::post('relaciones-familiares', [RelacionFamiliarApiController::class, 'store']);
        // Route::delete('relaciones-familiares/{relacion}', [RelacionFamiliarApiController::class, 'destroy']);
        
        // Regalos dados
        // Route::get('familiares/{familiar}/regalos-dados', [RegaloDadoApiController::class, 'index']);
        // Route::post('familiares/{familiar}/regalos-dados', [RegaloDadoApiController::class, 'store']);
        
        // Recordatorios
        // Route::get('familiares/{familiar}/recordatorios', [RecordatorioApiController::class, 'index']);
        // Route::post('familiares/{familiar}/recordatorios', [RecordatorioApiController::class, 'store']);
        
        // Cuotas mensuales
        // Route::get('cuotas-mensuales', [CuotaMensualApiController::class, 'index']);
        // Route::post('cuotas-mensuales', [CuotaMensualApiController::class, 'store']);
        
        // Configuración
        // Route::get('configuracion', [ConfiguracionApiController::class, 'index']);
        // Route::post('configuracion', [ConfiguracionApiController::class, 'update']);
    });
});

