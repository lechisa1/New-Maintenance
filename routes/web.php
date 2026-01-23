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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\IssueTypeController;
use App\Http\Controllers\BaseDataController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MyRequestController;



/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web','auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
    })->name('dashboard');
    
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('logout', [LoginController::class, 'showLogoutForm'])->name('logout.confirm');
    
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserController::class, 'edit'])->name('show');
        Route::put('/', [UserController::class, 'update'])->name('update');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [UserController::class, 'trashed'])->name('trashed');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{user}/force', [UserController::class, 'forceDelete'])->name('force-delete');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
        Route::get('/api/users', [UserController::class, 'getUsers'])->name('api.users');
    });
    
    // Role Management
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->withTrashed();
    Route::get('roles/{role}/users', [RoleController::class, 'users'])->name('roles.users');
    Route::post('roles/bulk-delete', [RoleController::class, 'bulkDestroy'])->name('roles.bulk-destroy');
    Route::delete('roles/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('roles.users.remove');
    Route::get('roles/check-name', [RoleController::class, 'checkName'])->name('roles.check-name');
    Route::patch('roles/{role}/restore', [RoleController::class, 'restore'])->name('roles.restore')->withTrashed();
    Route::delete('roles/{role}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete')->withTrashed();
    
    // Organizations
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}/clusters', [OrganizationController::class, 'clusters'])->name('organizations.clusters');
    Route::get('/clusters/{cluster}/divisions', [ClusterController::class, 'divisions'])->name('clusters.divisions');


    // API Endpoints
    Route::prefix('api')->group(function () {
        Route::prefix('organizations')->group(function () {
            Route::post('/', [OrganizationController::class, 'store']);
            Route::put('/{organization}', [OrganizationController::class, 'update']);
            Route::delete('/{organization}', [OrganizationController::class, 'destroy']);
            Route::get('/{organizationId}/clusters', [OrganizationController::class, 'getClusters']);
            Route::get('/{organizationId}/divisions', [OrganizationController::class, 'getDivisions']);
        });
        
        Route::prefix('clusters')->group(function () {
            Route::post('/', [ClusterController::class, 'store']);
            Route::put('/{cluster}', [ClusterController::class, 'update']);
            Route::delete('/{cluster}', [ClusterController::class, 'destroy']);
        });
        
        Route::prefix('divisions')->group(function () {
            Route::post('/', [DivisionController::class, 'store']);
            Route::put('/{division}', [DivisionController::class, 'update']);
            Route::delete('/{division}', [DivisionController::class, 'destroy']);
        });
    });
    Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])
    ->name('organizations.show');

    Route::get('/clusters/{cluster}/divisions', [ClusterController::class, 'showDivisions'])
    ->name('clusters.divisions');

    Route::get('/divisions/{division}', [DivisionController::class, 'show'])
    ->name('divisions.show');
    // Maintenance Requests
    Route::resource('maintenance-requests', MaintenanceRequestController::class);
    Route::get('/maintenance-requests/export', [MaintenanceRequestController::class, 'export'])->name('maintenance-requests.export');
    Route::get('/maintenance-requests/statistics', [MaintenanceRequestController::class, 'statistics'])->name('maintenance-requests.statistics');
    // Route::post('/maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance-requests.assign');
    Route::post('/maintenance-requests/{maintenanceRequest}/update-status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance-requests.update-status');
    Route::get('/maintenance-requests/{maintenanceRequest}/download-file/{file}', [MaintenanceRequestController::class, 'downloadFile'])->name('maintenance-requests.download-file');
    Route::delete('/maintenance-requests/{maintenanceRequest}/delete-file/{file}', [MaintenanceRequestController::class, 'deleteFile'])->name('maintenance-requests.delete-file');
    
    // Items (Equipment)
    Route::resource('items', ItemController::class);
    Route::get('/items/trashed', [ItemController::class, 'trashed'])->name('items.trashed');
    Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
    Route::post('/items/{id}/restore', [ItemController::class, 'restore'])->name('items.restore');
    Route::post('/items/{id}/force-delete', [ItemController::class, 'forceDelete'])->name('items.forceDelete');
    Route::post('/items/restore/all', [ItemController::class, 'restoreAll'])->name('items.restore.all');
    Route::post('/items/forceDelete/all', [ItemController::class, 'forceDeleteAll'])->name('items.forceDelete.all');
    
    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');


    //here base data for issue types

        Route::resource('issue-types', IssueTypeController::class);
    
    // Additional routes for toggling
    Route::post('/issue-types/{issueType}/toggle-status', [IssueTypeController::class, 'toggleStatus'])
        ->name('issue-types.toggle-status');
    
    Route::post('/issue-types/{issueType}/toggle-approval', [IssueTypeController::class, 'toggleApproval'])
        ->name('issue-types.toggle-approval');
Route::get('/base-data', [BaseDataController::class, 'index'])->name('base-data.index');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{maintenanceRequest}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{maintenanceRequest}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');


    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/task', [TaskController::class, 'index'])->name('user.task');


    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::patch('/permissions/{permission}/toggle', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle');

    Route::put('maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])
    ->name('maintenance-requests.assign');
    Route::get('/my-requests', [MyRequestController::class, 'index'])->name('my.requests');
    
});

// Default route - redirect to login if not authenticated, dashboard if authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});