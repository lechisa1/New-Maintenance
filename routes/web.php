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
use App\Http\Controllers\WorkLogController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\TechnicianReportController;



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
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('{user}/password', [PasswordChangeController::class, 'editPassword'])->name('password.edit');
    Route::put('{user}/password', [PasswordChangeController::class, 'updatePassword'])->name('password.update');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
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
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:users.view');
        Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('permission:users.create');
        Route::post('/', [UserController::class, 'store'])->name('store')->middleware('permission:users.create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show')->middleware('permission:users.view');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('permission:users.update');
        Route::put('/{user}', [UserController::class, 'update'])->name('update')->middleware('permission:users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:users.delete');
        Route::get('/trashed', [UserController::class, 'trashed'])->name('trashed')->middleware('permission:users.view');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore')->middleware('permission:users.delete');
        Route::delete('/{user}/force', [UserController::class, 'forceDelete'])->name('force-delete')->middleware('permission:users.delete');
        Route::get('/export', [UserController::class, 'export'])->name('export')->middleware('permission:users.view');
        Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics')->middleware('permission:users.view');
        Route::get('/api/users', [UserController::class, 'getUsers'])->name('api.users')->middleware('permission:users.view');
    });

    // Role Management
    Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:roles.view|roles.create|roles.update|roles.delete');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->withTrashed()->middleware('permission:roles.view');
    Route::get('roles/{role}/users', [RoleController::class, 'users'])->name('roles.users')->middleware('permission:roles.view');
    Route::post('roles/bulk-delete', [RoleController::class, 'bulkDestroy'])->name('roles.bulk-destroy')->middleware('permission:roles.delete');
    Route::delete('roles/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('roles.users.remove')->middleware('permission:roles.view');
    Route::get('roles/check-name', [RoleController::class, 'checkName'])->name('roles.check-name')->middleware('permission:roles.update');
    Route::patch('roles/{role}/restore', [RoleController::class, 'restore'])->name('roles.restore')->withTrashed()->middleware('permission:roles.delete');
    Route::delete('roles/{role}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete')->withTrashed()->middleware('permission:roles.delete');

    // Organizations
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index')->middleware('permission:organization_units.view');
    Route::get('/organizations/{organization}/clusters', [OrganizationController::class, 'clusters'])->name('organizations.clusters')->middleware('permission:organization_units.view');
    Route::get('/clusters/{cluster}/divisions', [ClusterController::class, 'divisions'])->name('clusters.divisions')->middleware('permission:organization_units.view');


    // API Endpoints
    Route::prefix('api')->group(function () {
        Route::prefix('organizations')->group(function () {
            Route::post('/', [OrganizationController::class, 'store'])->middleware('permission:organization_units.create');
            Route::put('/{organization}', [OrganizationController::class, 'update'])->middleware('permission:organization_units.update');
            Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->middleware('permission:organization_units.delete');
            Route::get('/{organizationId}/clusters', [OrganizationController::class, 'getClusters'])->middleware('permission:organization_units.view');
            Route::get('/{organizationId}/divisions', [OrganizationController::class, 'getDivisions'])->middleware('permission:organization_units.view');
        });

        Route::prefix('clusters')->group(function () {
            Route::post('/', [ClusterController::class, 'store'])->middleware('permission:organization_units.create');
            Route::put('/{cluster}', [ClusterController::class, 'update'])->middleware('permission:organization_units.update');
            Route::delete('/{cluster}', [ClusterController::class, 'destroy'])->middleware('permission:organization_units.delete');
        });

        Route::prefix('divisions')->group(function () {
            Route::post('/', [DivisionController::class, 'store'])->middleware('permission:organization_units.create');
            Route::put('/{division}', [DivisionController::class, 'update'])->middleware('permission:organization_units.update');
            Route::delete('/{division}', [DivisionController::class, 'destroy'])->middleware('permission:organization_units.delete');
        });
    });
    Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])
        ->name('organizations.show')->middleware('permission:organization_units.view');

    Route::get('/clusters/{cluster}/divisions', [ClusterController::class, 'showDivisions'])
        ->name('clusters.divisions')->middleware('permission:organization_units.view');

    Route::get('/divisions/{division}', [DivisionController::class, 'show'])
        ->name('divisions.show')->middleware('permission:organization_units.view');
    // Maintenance Requests

    Route::get('/maintenance-requests/export', [MaintenanceRequestController::class, 'export'])->name('maintenance-requests.export')->middleware('permission:maintenance_requests.view_all');
    Route::get('/maintenance-requests/statistics', [MaintenanceRequestController::class, 'statistics'])->name('maintenance-requests.statistics')->middleware('permission:maintenance_requests.view_all');
    // Route::post('/maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance-requests.assign');
    Route::post('/maintenance-requests/{maintenanceRequest}/update-status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance-requests.update-status')->middleware('permission:maintenance_requests.update');
    Route::get('/maintenance-requests/{maintenanceRequest}/download-file/{file}', [MaintenanceRequestController::class, 'downloadFile'])->name('maintenance-requests.download-file')->middleware('permission:maintenance_requests.view_all');
    Route::delete('/maintenance-requests/{maintenanceRequest}/delete-file/{file}', [MaintenanceRequestController::class, 'deleteFile'])->name('maintenance-requests.delete-file')->middleware('permission:maintenance_requests.update');
    Route::resource('maintenance-requests', MaintenanceRequestController::class)->middleware('permission:maintenance_requests.view_all|maintenance_requests.create|maintenance_requests.update|maintenance_requests.delete');
    Route::get('/maintenance/{maintenanceRequest}/report', [MaintenanceRequestController::class, 'downloadReport'])->name('maintenance.report')->middleware('permission:maintenance_requests.view_all');
    Route::get(
        '/maintenance-requests/{maintenanceRequest}/description-pdf',
        [MaintenanceRequestController::class, 'descriptionPdf']
    )->name('maintenance.description.pdf')->middleware('permission:maintenance_requests.view_all');

    // Items (Equipment)
    Route::resource('items', ItemController::class)->middleware('permission:equipment.view|equipment.create|equipment.update|equipment.delete');
    Route::get('/items/trashed', [ItemController::class, 'trashed'])->name('items.trashed')->middleware('permission:equipment.view');
    Route::get('/items/export', [ItemController::class, 'export'])->name('items.export')->middleware('permission:equipment.view');
    Route::post('/items/{id}/restore', [ItemController::class, 'restore'])->name('items.restore')->middleware('permission:equipment.delete');
    Route::post('/items/{id}/force-delete', [ItemController::class, 'forceDelete'])->name('items.forceDelete')->middleware('permission:equipment.delete');
    Route::post('/items/restore/all', [ItemController::class, 'restoreAll'])->name('items.restore.all')->middleware('permission:equipment.delete');
    Route::post('/items/forceDelete/all', [ItemController::class, 'forceDeleteAll'])->name('items.forceDelete.all')->middleware('permission:equipment.delete');



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
    //here route added for forwarding approval request to ICT Director after reviewed by technicians

    // routes/web.php

    Route::post(
        '/maintenance-requests/{maintenanceRequest}/request-approval',
        [ApprovalController::class, 'requestApproval']
    )
        ->name('maintenance-requests.request-approval')->middleware('permission:maintenance_requests.approve');

    Route::post(
        '/maintenance-requests/{maintenanceRequest}/forward-to-chairman',
        [ApprovalController::class, 'forwardToChairman']
    )
        ->name('maintenance-requests.forward-to-chairman')->middleware('permission:maintenance_requests.approve');

    Route::post(
        '/maintenance-requests/{maintenanceRequest}/reject-approval-request',
        [ApprovalController::class, 'rejectApprovalRequest']
    )
        ->name('maintenance-requests.reject-approval-request')->middleware('permission:maintenance_requests.reject');


    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/task', [TaskController::class, 'index'])->name('user.task');


    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::patch('/permissions/{permission}/toggle', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle');

    Route::put('maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])
        ->name('maintenance-requests.assign')->middleware('permission:maintenance_requests.assign');
    Route::post('maintenance-requests/{maintenanceRequest}/reject', [MaintenanceRequestController::class, 'reject'])
        ->name('maintenance-requests.reject')->middleware('permission:maintenance_requests.reject');
    Route::get('/my-requests', [MyRequestController::class, 'index'])->name('my.requests');

    // Work Logs routes
    Route::post('/work-logs', [WorkLogController::class, 'store'])->name('work-logs.store');
    Route::delete('/work-logs/{workLog}', [WorkLogController::class, 'destroy'])->name('work-logs.destroy');
    Route::get('/work-logs/request/{maintenanceRequest}', [WorkLogController::class, 'getForRequest'])->name('work-logs.by-request');

    Route::post('/work-logs/{workLog}/reject', [WorkLogController::class, 'rejectWorkLog'])
        ->name('work-logs.reject');

    Route::post('/work-logs/{workLog}/accept', [WorkLogController::class, 'acceptWorkLog'])
        ->name('work-logs.accept');

    /*
    |--------------------------------------------------------------------------
    | Technician Reports Routes
    |--------------------------------------------------------------------------
    */

    // Reports for assigners (those with assign permission)
    Route::prefix('reports')->name('reports.')->group(function () {
        // View all technician assignments with filtering
        Route::get('/technicians', [TechnicianReportController::class, 'index'])
            ->name('technician.index')
            ->middleware('permission:maintenance_requests.assign');

        // Download PDF report
        Route::get('/technicians/download', [TechnicianReportController::class, 'downloadReport'])
            ->name('technician.download')
            ->middleware('permission:maintenance_requests.assign');

        // Export CSV report
        Route::get('/technicians/export', [TechnicianReportController::class, 'exportReport'])
            ->name('technician.export')
            ->middleware('permission:maintenance_requests.assign');

        // Technician's own reports (for downloading their completed tasks)
        Route::get('/my', [TechnicianReportController::class, 'myReports'])
            ->name('my.index')
            ->middleware('permission:maintenance_requests.resolve');

        // Download my completed tasks PDF
        Route::get('/my/download', [TechnicianReportController::class, 'downloadMyReport'])
            ->name('my.download')
            ->middleware('permission:maintenance_requests.resolve');

        // Export my completed tasks CSV
        Route::get('/my/export', [TechnicianReportController::class, 'exportMyReport'])
            ->name('my.export')
            ->middleware('permission:maintenance_requests.resolve');
    });
});

// Default route - redirect to login if not authenticated, dashboard if authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
