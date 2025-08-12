<?php

use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentRCAController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Health check route for debugging
Route::get('/health', function () {
    try {
        // Test database connection
        DB::connection()->getPdo();
        $dbStatus = 'OK';
    } catch (Exception $e) {
        $dbStatus = 'FAILED: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'OK',
        'app_env' => env('APP_ENV'),
        'app_debug' => env('APP_DEBUG'),
        'app_key_set' => !empty(env('APP_KEY')),
        'database' => $dbStatus,
        'db_connection' => env('DB_CONNECTION'),
        'db_database' => env('DB_DATABASE'),
        'db_host' => env('DB_HOST'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'storage_writable' => is_writable(storage_path()),
        'cache_writable' => is_writable(storage_path('framework/cache')),
        'laravel_log_exists' => file_exists(storage_path('logs/laravel.log')),
        'last_error' => file_exists(storage_path('logs/laravel.log')) ? 
            substr(file_get_contents(storage_path('logs/laravel.log')), -1000) : 'No log file'
    ]);
})->name('health');

// Debug route to test basic Laravel functionality
Route::get('/debug', function () {
    try {
        // Test basic Laravel features
        $user_count = DB::table('users')->count();
        $categories = DB::table('categories')->count();
        $fault_types = DB::table('fault_types')->count();
        $resolution_teams = DB::table('resolution_teams')->count();
        
        return response()->json([
            'laravel_working' => true,
            'users_table_exists' => true,
            'user_count' => $user_count,
            'categories_count' => $categories,
            'fault_types_count' => $fault_types,
            'resolution_teams_count' => $resolution_teams,
            'auth_routes_loaded' => Route::has('login'),
            'incidents_create_route_exists' => Route::has('incidents.create'),
            'create_view_exists' => view()->exists('incidents.create'),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'laravel_working' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes that require only authentication (viewer and above)
Route::middleware(['auth', 'role:viewer'])->group(function () {
    // Redirect root to incidents
    Route::get('/dashboard', function () {
        return redirect()->route('incidents.index');
    })->name('dashboard');

    // View-only incident routes (viewer and above)
    Route::get('incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    
    // Logs page routes (viewer and above)
    Route::get('logs', [LogsController::class, 'index'])->name('logs.index');
    
    // Download RCA (viewer and above)
    Route::get('incidents/{incident}/download-rca', [IncidentRCAController::class, 'download'])->name('incidents.download-rca');

    // Profile routes (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Editor routes (editor and admin only)
Route::middleware(['auth', 'role:editor'])->group(function () {
    // Create and edit incidents
    Route::get('incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
    Route::patch('incidents/{incident}', [IncidentController::class, 'update']);
    
    // Export routes
    Route::get('incidents-export-preview', [IncidentController::class, 'exportPreview'])->name('incidents.export.preview');
    Route::get('incidents-export', [IncidentController::class, 'export'])->name('incidents.export');
    Route::get('logs-export', [LogsController::class, 'export'])->name('logs.export');
    
    // RCA generation
    Route::post('incidents/{incident}/generate-rca', [IncidentRCAController::class, 'generate'])->name('incidents.generate-rca');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Delete incidents (admin only)
    Route::delete('incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
