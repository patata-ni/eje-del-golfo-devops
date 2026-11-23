<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/version', \App\Http\Controllers\VersionController::class)->name('version');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard', fn () => redirect()->route('rutas'))
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/resultados', [SearchController::class, 'results'])->name('search.results');

Route::view('/nosotros', 'nosotros')->name('nosotros');

Route::post('/contacto', function(\Illuminate\Http\Request $request) {
    // 1. Validar los datos antes de guardar
    $request->validate([
        'nombre' => 'required|string|max:100',
        'correo' => 'required|email|max:255',
        'mensaje' => 'required|string',
    ]);

    // 2. Buscamos al Admin (Rol 1)
    $admin = \App\Models\Staff::where('rol_id', 1)->first();

    if ($admin) {
        \App\Models\Notification::create([
            'personal_id' => $admin->id_personal,
            'tipo' => 'mensaje_cliente',
            'titulo' => 'Mensaje de ' . $request->nombre,
            'mensaje' => "Correo: {$request->correo} | Tel: " . ($request->telefono ?? 'N/A') . " | Mensaje: {$request->mensaje}",
            'leida' => 0,
        ]);
    }

    return back()->with('success', '¡Mensaje enviado! Lo revisaremos pronto.');
})->name('contacto.store');

Route::view('/ayuda', 'ayuda')->name('ayuda');
Route::view('/privacidad', 'privacidad')->name('privacidad');
Route::view('/terminos', 'terminos')->name('terminos');

Route::get('/rutas', [SearchController::class, 'routes'])
    ->middleware('auth')
    ->name('rutas');
Route::get('/reservar/{id}', [App\Http\Controllers\ReservationController::class, 'create'])->middleware('auth')->name('reservar.confirmar');
Route::get('/boleto/{id}', [App\Http\Controllers\ReservationController::class, 'pdfTicket'])->middleware('auth')->name('boleto.pdf');

// --- Rutas de autenticación con Google ---
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// --- Rutas de administración ---
Route::middleware(['auth', 'role:1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/messages-clients', [App\Http\Controllers\Admin\NotificationController::class, 'clientMessages'])->name('messages.clients');

    // Generar pdfs
    Route::get('/pdf-report', [App\Http\Controllers\Admin\DashboardController::class, 'pdfReport'])->name('pdf');
    Route::get('/drivers/pdf', [App\Http\Controllers\Admin\DriverController::class, 'pdfReport'])->name('drivers.pdf');
    Route::get('/departures/pdf', [App\Http\Controllers\Admin\DepartureController::class, 'pdfReport'])->name('pdf.departures');

    // Rutas
    Route::get('/routes', [App\Http\Controllers\Admin\RouteManagementController::class, 'index'])->name('routes.index');
    Route::post('/routes', [App\Http\Controllers\Admin\RouteManagementController::class, 'store'])->name('routes.store');
    Route::put('/routes/{id}', [App\Http\Controllers\Admin\RouteManagementController::class, 'update'])->name('routes.update');
    Route::delete('/routes/{id}', [App\Http\Controllers\Admin\RouteManagementController::class, 'destroy'])->name('routes.destroy');

    // Unidades
    Route::get('/units', [App\Http\Controllers\Admin\UnitController::class, 'index'])->name('units.index');
    Route::post('/units', [App\Http\Controllers\Admin\UnitController::class, 'store'])->name('units.store');
    Route::put('/units/{id}', [App\Http\Controllers\Admin\UnitController::class, 'update'])->name('units.update');
    Route::delete('/units/{id}', [App\Http\Controllers\Admin\UnitController::class, 'destroy'])->name('units.destroy');
    
    // Personal
    Route::get('/staff', [App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [App\Http\Controllers\Admin\StaffController::class, 'store'])->name('staff.store');
    Route::put('/staff/{id}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}', [App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('staff.destroy');
    
    // Choferes
    Route::get('/drivers', [App\Http\Controllers\Admin\DriverController::class, 'index'])->name('drivers.index');
    Route::post('/drivers', [App\Http\Controllers\Admin\DriverController::class, 'store'])->name('drivers.store');
    Route::put('/drivers/{id}', [App\Http\Controllers\Admin\DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{id}', [App\Http\Controllers\Admin\DriverController::class, 'destroy'])->name('drivers.destroy');
    
    // Horarios
    Route::get('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{id}', [App\Http\Controllers\Admin\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Salidas
    Route::get('/departures', [App\Http\Controllers\Admin\DepartureController::class, 'index'])->name('departures.index');
    Route::post('/departures', [App\Http\Controllers\Admin\DepartureController::class, 'store'])->name('departures.store');
    Route::put('/departures/{id}', [App\Http\Controllers\Admin\DepartureController::class, 'update'])->name('departures.update');
    Route::delete('/departures/{id}', [App\Http\Controllers\Admin\DepartureController::class, 'destroy'])->name('departures.destroy');

    // Accesos al Sistema
    Route::get('/users', [App\Http\Controllers\Admin\SystemUserController::class, 'index'])->name('users.index');
    Route::post('/users', [App\Http\Controllers\Admin\SystemUserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\SystemUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\SystemUserController::class, 'destroy'])->name('users.destroy');

    // Incidencias
    Route::get('/incidents', [App\Http\Controllers\Admin\IncidentController::class, 'index'])->name('incidents.index');
    Route::post('/incidents', [App\Http\Controllers\Admin\IncidentController::class, 'store'])->name('incidents.store');
    Route::put('/incidents/{id}', [App\Http\Controllers\Admin\IncidentController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{id}', [App\Http\Controllers\Admin\IncidentController::class, 'destroy'])->name('incidents.destroy');

    // Notificaciones
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');

    // Rutas para inventario
    Route::get('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('inventory.store');
    

});

// --- Rutas de mecánico ---
Route::middleware(['auth', 'role:1,2'])->prefix('mechanic')->name('mechanic.')->group(function () {
    Route::get('/', [App\Http\Controllers\Mechanic\MechanicController::class, 'index'])->name('dashboard');
    
    // Rutas para reportes
    Route::get('/reports', [App\Http\Controllers\Mechanic\MechanicController::class, 'reports'])->name('reports.index');

    // Rutas para mantenimiento
    Route::get('/maintenance', [App\Http\Controllers\Mechanic\MechanicController::class, 'maintenance'])->name('maintenance.index');
    Route::post('/maintenance', [App\Http\Controllers\Mechanic\MechanicController::class, 'createMaintenance'])->name('maintenance.store'); 
    Route::put('/maintenance/{id}', [App\Http\Controllers\Mechanic\MechanicController::class, 'update'])->name('maintenance.update');

    // Inventario del mecánico
    Route::get('/inventory', [App\Http\Controllers\Mechanic\MechanicController::class, 'inventory'])->name('inventory.index'); // <-- AGREGA ESTO
});

require __DIR__.'/auth.php';
