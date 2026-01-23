<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status'); // '1' for active, '0' for inactive
    $perPage = $request->input('per_page', 10);
    
    $roles = Role::withCount(['permissions', 'users'])
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        })
        // Simple 1/0 status filter
        ->when($status !== null && $status !== '', function ($query) use ($status) {
            return $query->where('is_active', $status);
        })
        ->orderByRaw("CASE WHEN name = 'super-admin' THEN 1 ELSE 2 END")
        ->orderBy('created_at', 'desc')
        ->paginate($perPage)
        ->withQueryString();

    return view('admin.roles.index', compact('roles'));
}

/**
 * Show the form for creating a new resource.
 */
public function create()
{
    // Filter only active permissions and group them by resource for the UI
    $permissions = Permission::where('is_active', true) // <--- Add this line
        ->orderBy('resource')
        ->orderBy('name')
        ->get()
        ->groupBy('resource')
        ->map(function ($group) {
            return $group->mapWithKeys(function ($perm) {
                // Extract action (e.g., 'view' from 'users.view')
                $parts = explode('.', $perm->name);
                $action = isset($parts[1]) ? $parts[1] : 'other';
                return [$action => $perm];
            });
        });

    $guards = array_keys(config('auth.guards'));

    return view('admin.roles.create', compact('permissions', 'guards'));
}

public function store(Request $request)
{
    $request->merge([
        'name' => Str::slug($request->name),
    ]);

    $validated = $this->validateRole($request);

    DB::beginTransaction();

    try {
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'], // IMPORTANT
            'dashboard_route' => $validated['dashboard_route'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_system_role' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'role_id' => $role->id,
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();
        logger()->error($e);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}






    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        // $this->authorize('view', $role);

        $role->load(['permissions', 'users' => function ($query) {
            $query->latest()->take(10);
        }]);

        $permissionGroups = $role->permissions->groupBy('resource');

        return view('admin.roles.show', compact('role', 'permissionGroups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Role $role)
{
    $permissions = Permission::where('is_active', true)->orderBy('resource')
        ->orderBy('name')
        ->get()
        ->groupBy('resource');

    $guards = array_keys(config('auth.guards'));
    
    // Extract all IDs as strings for Alpine.js compatibility
    $allPermissionIds = Permission::pluck('id')->map(fn($id) => (string)$id)->toArray();

    $role->load('permissions');

    return view('admin.roles.edit', compact(
        'role',
        'permissions',
        'guards',
        'allPermissionIds' // Pass this to the view
    ));
}


    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Role $role)
{
    // $this->authorize('update', $role);

    $request->merge([
        'name' => Str::slug($request->name),
    ]);

    $validated = $this->validateRole($request, $role);

    // Prevent guard change if users exist
    if (
        $role->guard_name !== $validated['guard_name']
        && $role->users()->exists()
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Cannot change guard name while role has assigned users.'),
            ], 422);
        }

        return back()
            ->withInput()
            ->with('error', __('Cannot change guard name while role has assigned users.'));
    }

    DB::beginTransaction();

    try {
        $role->update([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'],
            'dashboard_route' => $validated['dashboard_route'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        // ✔ SAME AS STORE
        $role->syncPermissions($validated['permissions'] ?? []);

        DB::commit();

        // ✅ AJAX response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role updated successfully.'),
            ]);
        }

        // ✅ Normal form response
        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role updated successfully.'));

    } catch (\Throwable $e) {
        DB::rollBack();

        logger()->error('Role update failed', [
            'role_id' => $role->id,
            'error' => $e->getMessage(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Failed to update role. Please try again.'),
            ], 500);
        }

        return back()
            ->withInput()
            ->with('error', __('Failed to update role. Please try again.'));
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        if (!$role->is_deletable) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('This role cannot be deleted.'));
        }

        DB::beginTransaction();

        try {
            $role->permissions()->detach();
            $role->delete();

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', __('Role deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            logger()->error('Role deletion failed: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('Failed to delete role. Please try again.'));
        }
    }

    /**
     * Show role users.
     */
    public function users(Role $role, Request $request)
    {
        $this->authorize('view', $role);

        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);

        $users = $role->users()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.roles.users', compact('role', 'users'));
    }

    /**
     * Bulk delete roles.
     */
    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', Role::class);

        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:roles,id'],
        ]);

        $roles = Role::whereIn('id', $request->ids)
            ->where('is_system_role', false)
            ->whereDoesntHave('users')
            ->get();

        if ($roles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('No deletable roles selected.')
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($roles as $role) {
                $role->permissions()->detach();
                $role->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __(':count roles deleted successfully.', ['count' => $roles->count()])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            logger()->error('Bulk role deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete roles.')
            ], 500);
        }
    }

    /**
     * Validate role data.
     */
protected function validateRole(Request $request, Role $role = null): array
{
    $rules = [
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-z0-9-]+$/',
            Rule::unique('roles', 'name')->ignore($role?->id),
        ],
        'guard_name' => [
            'required',
            'string',
            Rule::in(array_keys(config('auth.guards'))),
        ],
        'dashboard_route' => [
            'nullable',
            'string',
            'max:255',
            'alpha_dash',
        ],
        'description' => [
            'nullable',
            'string',
            'max:500',
        ],
        'permissions' => [
            'nullable',
            'array',
        ],
        'permissions.*' => [
            'uuid', // ✅ THIS WAS MISSING
            'exists:permissions,id',
        ],
    ];

    $messages = [
        'name.regex' => __('Role name must contain only lowercase letters, numbers, and hyphens.'),
    ];

    return $request->validate($rules, $messages);
}

        public function checkName(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'except' => ['nullable', 'exists:roles,id']
        ]);

        $name = Str::slug($request->name, '-');
        $query = Role::where('name', $name);

        if ($request->filled('except')) {
            $query->where('id', '!=', $request->except);
        }

        return response()->json([
            'available' => !$query->exists(),
            'suggested_name' => $name,
            'exists' => $query->exists()
        ]);
    }
        public function removeUser(Role $role, $userId)
    {
        $this->authorize('update', $role);

        try {
            $userModel = config('auth.providers.users.model');
            $user = app($userModel)->findOrFail($userId);
            
            $user->removeRole($role);

            return response()->json([
                'success' => true,
                'message' => __('User removed from role successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to remove user from role.')
            ], 500);
        }
    }
}