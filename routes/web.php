<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\Farmer\ListingController as FarmerListingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/listings', [BrowseController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [BrowseController::class, 'show'])->name('listings.show');
Route::get('/compare', [CompareController::class, 'index'])->name('compare');
Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/dashboard', function () {
    return redirect()->route(Auth::user()->dashboardRoute());
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', fn () => view('farmer.dashboard'))->name('dashboard');
    Route::patch('listings/{listing}/stock', [FarmerListingController::class, 'updateStock'])->name('listings.stock');
    Route::resource('listings', FarmerListingController::class);
});

Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', fn () => view('buyer.dashboard'))->name('dashboard');
    Route::post('/listings/{listing}/contact', [InboxController::class, 'store'])->name('contact');
    Route::post('/listings/{listing}/order', [OrderController::class, 'create'])
        ->middleware('throttle:20,1')->name('order.request');
});

Route::middleware('auth')->group(function () {
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/{conversation}', [InboxController::class, 'show'])->name('inbox.show');
    Route::get('/inbox/{conversation}/poll', [InboxController::class, 'poll'])->name('inbox.poll');
    Route::post('/inbox/{conversation}/reply', [InboxController::class, 'reply'])
        ->middleware('throttle:30,1')->name('inbox.reply');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/{action}', [OrderController::class, 'update'])
        ->where('action', 'accept|decline|complete|cancel')->name('orders.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.overview'))->name('dashboard');
    Route::get('/overview', [AdminController::class, 'index'])->name('overview');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/verify', [AdminController::class, 'verifyFarmer'])->name('users.verify');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [AdminController::class, 'activate'])->name('users.activate');
    Route::get('/listings', [AdminController::class, 'listings'])->name('listings');
    Route::post('/listings/{listing}/hide', [AdminController::class, 'hideListing'])->name('listings.hide');
    Route::post('/listings/{listing}/relist', [AdminController::class, 'relistListing'])->name('listings.relist');
    Route::delete('/listings/{listing}', [AdminController::class, 'removeListing'])->name('listings.remove');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/auth.php';

Route::get('/health', function () {
    $dbOk = false;
    $dbError = null;
    $tableCount = 0;

    try {
        DB::connection()->getPdo();
        $dbOk = true;
        if (config('database.default') === 'sqlite') {
            $tableCount = count(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"));
        } else {
            $tableCount = count(DB::select('SHOW TABLES'));
        }
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }

    return response()->json([
        'status' => $dbOk ? 'healthy' : 'degraded',
        'php' => PHP_VERSION,
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_key_set' => ! empty(config('app.key')),
        'db_connection' => config('database.default'),
        'db_connected' => $dbOk,
        'db_error' => $dbError,
        'table_count' => $tableCount,
    ]);
});

Route::get('/deploy/init', function (Request $request) {
    $expectedKey = config('app.key');
    if (! empty($expectedKey) && $request->query('key') !== $expectedKey && ! $request->query('force')) {
        abort(403, 'Unauthorized setup access key. Provide ?key={APP_KEY} or ?force=1');
    }

    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => 'Database successfully migrated and seeded with 9 categories & demo accounts.',
            'migrate' => $migrateOutput,
            'seed' => $seedOutput,
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/storage/{path}', function (string $path) {
    $file = storage_path('app/public/'.$path);
    if (file_exists($file)) {
        return response()->file($file);
    }

    $basename = basename($path);
    $pub = public_path('images/produce/'.$basename);
    if (file_exists($pub)) {
        return response()->file($pub);
    }

    $demo = resource_path('demo-images/produce/'.$basename);
    if (file_exists($demo)) {
        return response()->file($demo);
    }

    abort(404);
})->where('path', '.*');
