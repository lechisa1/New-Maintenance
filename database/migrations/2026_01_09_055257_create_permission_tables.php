<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames   = config('permission.table_names');
        $columnNames  = config('permission.column_names');
        $teams        = config('permission.teams');

        $pivotRole       = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';
        $modelKey        = $columnNames['model_morph_key'];

        if (empty($tableNames)) {
            throw new Exception('config/permission.php not loaded. Run php artisan config:clear');
        }

        /**
         * --------------------------------------------------
         * permissions
         * --------------------------------------------------
         */
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('resource')->nullable();
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        /**
         * --------------------------------------------------
         * roles
         * --------------------------------------------------
         */
        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->uuid('id')->primary();

            if ($teams) {
                $table->uuid($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key']);
            }

            $table->string('name');
            $table->string('guard_name');

            // OPTIONAL (RECOMMENDED)
            $table->string('dashboard_route')->nullable();

            $table->timestamps();

            if ($teams) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        /**
         * --------------------------------------------------
         * model_has_permissions
         * --------------------------------------------------
         */
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use (
            $tableNames,
            $pivotPermission,
            $modelKey,
            $teams,
            $columnNames
        ) {
            $table->uuid($pivotPermission);
            $table->string('model_type');
            $table->uuid($modelKey);

            $table->index([$modelKey, 'model_type']);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->uuid($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key']);

                $table->primary([
                    $columnNames['team_foreign_key'],
                    $pivotPermission,
                    $modelKey,
                    'model_type'
                ]);
            } else {
                $table->primary([$pivotPermission, $modelKey, 'model_type']);
            }
        });

        /**
         * --------------------------------------------------
         * model_has_roles
         * --------------------------------------------------
         */
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use (
            $tableNames,
            $pivotRole,
            $modelKey,
            $teams,
            $columnNames
        ) {
            $table->uuid($pivotRole);
            $table->string('model_type');
            $table->uuid($modelKey);

            $table->index([$modelKey, 'model_type']);

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->uuid($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key']);

                $table->primary([
                    $columnNames['team_foreign_key'],
                    $pivotRole,
                    $modelKey,
                    'model_type'
                ]);
            } else {
                $table->primary([$pivotRole, $modelKey, 'model_type']);
            }
        });

        /**
         * --------------------------------------------------
         * role_has_permissions
         * --------------------------------------------------
         */
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use (
            $tableNames,
            $pivotRole,
            $pivotPermission
        ) {
            $table->uuid($pivotPermission);
            $table->uuid($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole]);
        });

        app('cache')
            ->store(config('permission.cache.store') !== 'default'
                ? config('permission.cache.store')
                : null
            )
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
