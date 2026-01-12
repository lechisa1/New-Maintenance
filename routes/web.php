<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MaintenanceRequestController;



// Organization Routes
// Route::middleware(['auth'])->group(function () {
    // Main organization management page
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    
    // Organization CRUD API endpoints (for AJAX)
    Route::prefix('api/organizations')->group(function () {
        Route::post('/', [OrganizationController::class, 'store']);
        Route::put('/{organization}', [OrganizationController::class, 'update']);
        Route::delete('/{organization}', [OrganizationController::class, 'destroy']);
        Route::get('/{organizationId}/clusters', [OrganizationController::class, 'getClusters']);
        Route::get('/{organizationId}/divisions', [OrganizationController::class, 'getDivisions']);
    });

    // Cluster CRUD API endpoints (for AJAX)
    Route::prefix('api/clusters')->group(function () {
        Route::post('/', [ClusterController::class, 'store']);
        Route::put('/{cluster}', [ClusterController::class, 'update']);
        Route::delete('/{cluster}', [ClusterController::class, 'destroy']);
    });

    // Division CRUD API endpoints (for AJAX)
    Route::prefix('api/divisions')->group(function () {
        Route::post('/', [DivisionController::class, 'store']);
        Route::put('/{division}', [DivisionController::class, 'update']);
        Route::delete('/{division}', [DivisionController::class, 'destroy']);
    });
    Route::get('/organizations/{organization}/clusters', [OrganizationController::class, 'clusters']);
Route::get('/clusters/{cluster}/divisions', [ClusterController::class, 'divisions']);
// });
// User Management Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Additional routes
    Route::get('/trashed', [UserController::class, 'trashed'])->name('users.trashed');
    Route::post('/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/{user}/force', [UserController::class, 'forceDelete'])->name('force-delete');
    Route::get('/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('users.statistics');
    
    // API endpoints for AJAX
    Route::get('/api/users', [UserController::class, 'getUsers'])->name('api.users');
});

// Profile routes (for users to edit their own profile)
Route::prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    Route::get('/', [UserController::class, 'edit'])->name('show');
    Route::put('/', [UserController::class, 'update'])->name('update');
});
// Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/roles', [App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');

// Cluster Routes

//here is route for request with all crude

// Maintenance Request Routes
Route::resource('maintenance-requests', \App\Http\Controllers\MaintenanceRequestController::class);

// Additional routes for maintenance requests
Route::get('/maintenance-requests/export', [MaintenanceRequestController::class, 'export'])->name('maintenance-requests.export');
Route::get('/maintenance-requests/statistics', [MaintenanceRequestController::class, 'statistics'])->name('maintenance-requests.statistics');
Route::post('/maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance-requests.assign');
Route::post('/maintenance-requests/{maintenanceRequest}/update-status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance-requests.update-status');
Route::get('/maintenance-requests/{maintenanceRequest}/download-file/{file}', [MaintenanceRequestController::class, 'downloadFile'])->name('maintenance-requests.download-file');
Route::delete('/maintenance-requests/{maintenanceRequest}/delete-file/{file}', [MaintenanceRequestController::class, 'deleteFile'])->name('maintenance-requests.delete-file');

// Route::get('/organizations', [App\Http\Controllers\RoleController::class, 'organizations'])->name('organizations');
Route::get('/forms', [App\Http\Controllers\RoleController::class, 'forms'])->name('forms');
Route::get('/detail-request', [App\Http\Controllers\RoleController::class, 'detailRequest'])->name('detail-request');
Route::get('/assignments', [App\Http\Controllers\RoleController::class, 'assignments'])->name('assignments');
// Route::get('/items', [App\Http\Controllers\RoleController::class, 'items'])->name('items');
Route::get('/equipments', [App\Http\Controllers\RoleController::class, 'equpments'])->name('equipments');




// Equipment (Item) Routes
Route::resource('/items', \App\Http\Controllers\ItemController::class);

// Additional routes for items
Route::get('/items/trashed', [ItemController::class, 'trashed'])->name('items.trashed');
Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
Route::post('/items/{id}/restore', [ItemController::class, 'restore'])->name('items.restore');
Route::post('/items/{id}/force-delete', [ItemController::class, 'forceDelete'])->name('items.forceDelete');
Route::post('/items/restore/all', [ItemController::class, 'restoreAll'])->name('items.restore.all');
Route::post('/items/forceDelete/all', [ItemController::class, 'forceDeleteAll'])->name('items.forceDelete.all');


// dashboard pages
Route::get('/', function () {
    return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
})->name('dashboard');
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');
Route::get('/test-table', function () {
    return view('test-table');
});
// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');






















