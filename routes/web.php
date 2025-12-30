<?php

use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentRCAController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RcaController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TemporarySiteController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SmartIncidentParserController;
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

// Test route for permissions
Route::get('/test-permissions', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }

    $user = auth()->user();
    return response()->json([
        'user_role' => $user->role,
        'isAdmin' => $user->isAdmin(),
        'isEditor' => $user->isEditor(),
        'isViewer' => $user->isViewer(),
        'canEditIncidents' => $user->canEditIncidents(),
        'incidents_create_url' => route('incidents.create'),
        'has_create_route' => Route::has('incidents.create'),
    ]);
})->middleware('auth');

// Test route for AI API connection
Route::get('/test-ai', function () {
    try {
        $service = new \App\Services\AIIncidentParserService();
        $result = $service->testConnection();
        return response()->json($result);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// Public home/network dashboard - no auth required
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Editor routes (editor and admin only) - Must come before viewer routes to avoid conflicts
Route::middleware(['auth', 'role:editor'])->group(function () {
    // Create and edit incidents (specific routes first)
    Route::get('incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('incidents/{incident}/edit', [IncidentController::class, 'edit'])->where('incident', '[0-9]+')->name('incidents.edit');
    Route::put('incidents/{incident}', [IncidentController::class, 'update'])->where('incident', '[0-9]+')->name('incidents.update');
    Route::patch('incidents/{incident}', [IncidentController::class, 'update'])->where('incident', '[0-9]+');
    Route::put('incidents/{incident}/close', [IncidentController::class, 'close'])->where('incident', '[0-9]+')->name('incidents.close');
    Route::post('incidents/{incident}/timeline', [IncidentController::class, 'addTimelineUpdate'])->where('incident', '[0-9]+')->name('incidents.timeline.add');

    // Import routes
    Route::get('incidents/import', [IncidentController::class, 'showImport'])->name('incidents.import');
    Route::post('incidents/import', [IncidentController::class, 'import'])->name('incidents.import.store');
    
    // Export routes
    Route::get('incidents-export-preview', [IncidentController::class, 'exportPreview'])->name('incidents.export.preview');
    Route::get('incidents-export', [IncidentController::class, 'export'])->name('incidents.export');
    Route::get('logs-export', [LogsController::class, 'export'])->name('logs.export');

    // Recurring incidents analysis
    Route::get('logs/recurring-incidents', [LogsController::class, 'recurringIncidents'])->name('logs.recurring-incidents');
    Route::get('logs/incidents-by-summary', [LogsController::class, 'incidentsBySummary'])->name('logs.incidents-by-summary');

    // RCA generation
    Route::post('incidents/{incident}/generate-rca', [IncidentRCAController::class, 'generate'])->where('incident', '[0-9]+')->name('incidents.generate-rca');

    // Smart Incident Parser routes (editor and admin only)
    Route::get('smart-parser', [SmartIncidentParserController::class, 'index'])->name('smart-parser.index');
    Route::post('smart-parser/parse', [SmartIncidentParserController::class, 'parse'])->name('smart-parser.parse');
    Route::post('smart-parser/store', [SmartIncidentParserController::class, 'store'])->name('smart-parser.store');

    // RCA Management (editor and admin only)
    Route::get('rcas/create', [RcaController::class, 'create'])->name('rcas.create');
    Route::post('rcas', [RcaController::class, 'store'])->name('rcas.store');
    Route::get('rcas/{rca}/edit', [RcaController::class, 'edit'])->where('rca', '[0-9]+')->name('rcas.edit');
    Route::put('rcas/{rca}', [RcaController::class, 'update'])->where('rca', '[0-9]+')->name('rcas.update');
    Route::patch('rcas/{rca}', [RcaController::class, 'update'])->where('rca', '[0-9]+');
    Route::delete('rcas/{rca}', [RcaController::class, 'destroy'])->where('rca', '[0-9]+')->name('rcas.destroy');
});

// Public routes that require only authentication (viewer and above)
Route::middleware(['auth', 'role:viewer'])->group(function () {
    // Redirect dashboard to home
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    // View-only incident routes (viewer and above) - Wildcard routes last
    Route::get('incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('incidents/{incident}', [IncidentController::class, 'show'])->where('incident', '[0-9]+')->name('incidents.show');
    Route::get('incidents/{incident}/copy-text', [IncidentController::class, 'getCopyText'])->where('incident', '[0-9]+')->name('incidents.copy-text');
    
    // Logs page routes (viewer and above)
    Route::get('logs', [LogsController::class, 'index'])->name('logs.index');

    // Reports page routes (viewer and above)
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

    // Phone Book / Contacts routes (viewer and above)
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->where('contact', '[0-9]+')->name('contacts.show');

    // Temporary Sites routes
    Route::get('temporary-sites', [TemporarySiteController::class, 'index'])->name('temporary-sites.index');
    Route::get('temporary-sites/{temporarySite}', [TemporarySiteController::class, 'show'])->where('temporarySite', '[0-9]+')->name('temporary-sites.show');

    // Sites routes
    Route::get('sites', [SiteController::class, 'index'])->name('sites.index');
    Route::get('sites/{site}', [SiteController::class, 'show'])->where('site', '[0-9]+')->name('sites.show');

    // FBB Islands routes
    Route::get('fbb-islands', [App\Http\Controllers\FbbIslandController::class, 'index'])->name('fbb-islands.index');
    Route::get('fbb-islands/{fbbIsland}', [App\Http\Controllers\FbbIslandController::class, 'show'])->where('fbbIsland', '[0-9]+')->name('fbb-islands.show');

    // ISP Links routes (viewer and above)
    Route::get('isp/dashboard', [App\Http\Controllers\IspLinkController::class, 'dashboard'])->name('isp.dashboard');
    Route::get('isp', [App\Http\Controllers\IspLinkController::class, 'index'])->name('isp.index');
    Route::get('isp/{ispLink}', [App\Http\Controllers\IspLinkController::class, 'show'])->where('ispLink', '[0-9]+')->name('isp.show');

    // RCA view routes (viewer and above)
    Route::get('rcas', [RcaController::class, 'index'])->name('rcas.index');
    Route::get('rcas/{rca}', [RcaController::class, 'show'])->where('rca', '[0-9]+')->name('rcas.show');

    // Download RCA (viewer and above)
    Route::get('incidents/{incident}/download-rca', [IncidentRCAController::class, 'download'])->where('incident', '[0-9]+')->name('incidents.download-rca');

    // Profile routes (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Delete incidents (admin only)
    Route::delete('incidents/{incident}', [IncidentController::class, 'destroy'])->where('incident', '[0-9]+')->name('incidents.destroy');
    
    // User management routes (admin only)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Contact management routes (admin only)
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ContactController::class, 'destroyBulk'])->name('bulk-delete');
    });

    // Temporary Sites management routes (admin only)
    Route::prefix('temporary-sites')->name('temporary-sites.')->group(function () {
        Route::get('/create', [TemporarySiteController::class, 'create'])->name('create');
        Route::post('/', [TemporarySiteController::class, 'store'])->name('store');
        Route::get('/{temporarySite}/edit', [TemporarySiteController::class, 'edit'])->name('edit');
        Route::put('/{temporarySite}', [TemporarySiteController::class, 'update'])->name('update');
        Route::delete('/{temporarySite}', [TemporarySiteController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [TemporarySiteController::class, 'destroyBulk'])->name('bulk-delete');
        Route::get('/import', [TemporarySiteController::class, 'importForm'])->name('import');
        Route::post('/import', [TemporarySiteController::class, 'import'])->name('import.process');
        Route::post('/{temporarySite}/toggle-status', [TemporarySiteController::class, 'toggleTechStatus'])->name('toggle-status');
    });

    // Sites management routes (admin only)
    Route::prefix('sites')->name('sites.')->group(function () {
        Route::get('/create', [SiteController::class, 'create'])->name('create');
        Route::post('/', [SiteController::class, 'store'])->name('store');
        Route::get('/{site}/edit', [SiteController::class, 'edit'])->name('edit');
        Route::put('/{site}', [SiteController::class, 'update'])->name('update');
        Route::delete('/{site}', [SiteController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [SiteController::class, 'destroyBulk'])->name('bulk-delete');
    });

    // FBB Islands management routes (admin only)
    Route::prefix('fbb-islands')->name('fbb-islands.')->group(function () {
        Route::get('/create', [App\Http\Controllers\FbbIslandController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\FbbIslandController::class, 'store'])->name('store');
        Route::get('/{fbbIsland}/edit', [App\Http\Controllers\FbbIslandController::class, 'edit'])->name('edit');
        Route::put('/{fbbIsland}', [App\Http\Controllers\FbbIslandController::class, 'update'])->name('update');
        Route::delete('/{fbbIsland}', [App\Http\Controllers\FbbIslandController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [App\Http\Controllers\FbbIslandController::class, 'destroyBulk'])->name('bulk-delete');
    });

    // ISP Links management routes (admin only)
    Route::prefix('isp')->name('isp.')->group(function () {
        Route::get('/create', [App\Http\Controllers\IspLinkController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\IspLinkController::class, 'store'])->name('store');
        Route::get('/{ispLink}/edit', [App\Http\Controllers\IspLinkController::class, 'edit'])->name('edit');
        Route::put('/{ispLink}', [App\Http\Controllers\IspLinkController::class, 'update'])->name('update');
        Route::delete('/{ispLink}', [App\Http\Controllers\IspLinkController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/test-dashboard.php';
