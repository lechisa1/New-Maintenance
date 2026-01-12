use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\DivisionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('organizations')->group(function () {
        Route::get('/', [OrganizationController::class, 'index']);
        Route::post('/', [OrganizationController::class, 'store']);
        Route::put('/{organization}', [OrganizationController::class, 'update']);
        Route::delete('/{organization}', [OrganizationController::class, 'destroy']);
        Route::get('/{organizationId}/clusters', [OrganizationController::class, 'getClusters']);
        Route::get('/{organizationId}/divisions', [OrganizationController::class, 'getDivisions']);
    });

    // Cluster Routes
    Route::prefix('clusters')->group(function () {
        Route::post('/', [ClusterController::class, 'store']);
        Route::put('/{cluster}', [ClusterController::class, 'update']);
        Route::delete('/{cluster}', [ClusterController::class, 'destroy']);
    });

    // Division Routes
    Route::prefix('divisions')->group(function () {
        Route::post('/', [DivisionController::class, 'store']);
        Route::put('/{division}', [DivisionController::class, 'update']);
        Route::delete('/{division}', [DivisionController::class, 'destroy']);
    });
});