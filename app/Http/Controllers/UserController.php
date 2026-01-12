<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Cluster;
use App\Models\Organization;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $filters = $request->only(['search', 'division_id', 'cluster_id', 'role', 'status']);

    $usersQuery = User::with(['division', 'cluster', 'roles'])
        ->filter($filters);

    // Paginated users
    $users = (clone $usersQuery)
        ->latest()
        ->paginate(2)
        ->withQueryString();

    // ✅ STATISTICS
    $totalUsers = User::count();
    $activeUsers = User::whereNotNull('email_verified_at')->count();
    $inactiveUsers = User::whereNull('email_verified_at')->count();
    $adminUsers = 10;

    // Filters data
    $divisions = Division::orderBy('name')->get(['id', 'name']);
    $clusters = Cluster::orderBy('name')->get(['id', 'name']);
    $organizations = Organization::orderBy('name')->get(['id', 'name']);
    $roles = Role::orderBy('name')->get(['id', 'name']);

    return view('users.index', compact(
        'users',
        'divisions',
        'clusters',
        'organizations',
        'roles',
        'filters',
        'totalUsers',
        'activeUsers',
        'inactiveUsers',
        'adminUsers'
    ));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('create', User::class);

        $divisions = Division::orderBy('name')->get(['id', 'name']);
        $clusters = Cluster::orderBy('name')->get(['id', 'name']);
        $organizations = Organization::orderBy('name')->get(['id', 'name']);
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.create', compact('divisions', 'clusters', 'organizations', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // $this->authorize('create', User::class);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'id' => Str::uuid(),
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'division_id' => $request->division_id,
                'cluster_id' => $request->cluster_id,
                'email_verified_at' => $request->boolean('email_verified') ? now() : null,
            ]);

            // Assign roles
            $user->syncRoles($request->roles);

            // Send welcome email if requested
            if ($request->boolean('send_welcome_email')) {
                $tempPassword = $request->password;
                Mail::to($user->email)->queue(new UserWelcomeMail($user, $tempPassword));
            }

            // Log activity
            // activity()
            //     ->causedBy(auth()->user())
            //     ->performedOn($user)
            //     ->withProperties([
            //         'email' => $user->email,
            //         'roles' => $request->roles
            //     ])
            //     ->log('created user');

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // $this->authorize('view', $user);

        $user->load(['division', 'cluster', 'roles', 'organization']);
        
        // Get user activity logs
        // $activities = $user->activities()
        //     ->with('causer')
        //     ->latest()
        //     ->take(10)
        //     ->get();

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // $this->authorize('update', $user);

        $divisions = Division::orderBy('name')->get(['id', 'name']);
        $clusters = Cluster::orderBy('name')->get(['id', 'name']);
        $organizations = Organization::orderBy('name')->get(['id', 'name']);
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.edit', compact('user', 'divisions', 'clusters', 'organizations', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        DB::beginTransaction();

        try {
            // Prepare update data
            $updateData = [
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'division_id' => $request->division_id,
                'cluster_id' => $request->cluster_id,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update email verification status
            if ($request->has('is_active')) {
                $updateData['email_verified_at'] = $request->boolean('is_active') ? now() : null;
            }

            // Update user
            $user->update($updateData);

            // Sync roles if provided (admin only)
            if (auth()->user()->hasRole('admin') && $request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties($request->validated())
                ->log('updated user');

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();

        try {
            // Store user info for logging
            $userEmail = $user->email;
            
            // Soft delete the user
            $user->delete();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['email' => $userEmail])
                ->log('deleted user');

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User deletion failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $this->authorize('restore', $user);

        DB::beginTransaction();

        try {
            $user->restore();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('restored user');

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User restored successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User restoration failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to restore user. Please try again.');
        }
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $this->authorize('forceDelete', $user);

        // Prevent self-permanent deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot permanently delete your own account.');
        }

        DB::beginTransaction();

        try {
            // Store user info for logging
            $userEmail = $user->email;
            
            // Permanently delete
            $user->forceDelete();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['email' => $userEmail])
                ->log('permanently deleted user');

            DB::commit();

            return redirect()->route('users.index', ['trashed' => true])
                ->with('success', 'User permanently deleted.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User permanent deletion failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to permanently delete user. Please try again.');
        }
    }

    /**
     * Show trashed users.
     */
    public function trashed(Request $request)
    {
        $this->authorize('viewTrashed', User::class);

        $users = User::onlyTrashed()
            ->with(['division', 'cluster', 'roles'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('users.trashed', compact('users'));
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $this->authorize('export', User::class);

        $filters = $request->only(['search', 'division_id', 'cluster_id', 'role', 'status']);
        
        $users = User::with(['division', 'cluster', 'roles'])
            ->filter($filters)
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=users_' . date('Y-m-d_H-i-s') . '.csv',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'ID',
                'Full Name',
                'Email',
                'Phone',
                'Division',
                'Cluster',
                'Organization',
                'Roles',
                'Status',
                'Created At',
                'Last Updated'
            ]);

            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->full_name,
                    $user->email,
                    $user->phone,
                    $user->division?->name,
                    $user->cluster?->name,
                    $user->organization?->name,
                    $user->roles->pluck('name')->join(', '),
                    $user->isActive() ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get users for Select2/autocomplete.
     */
    public function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('q')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->has('cluster_id')) {
            $query->where('cluster_id', $request->cluster_id);
        }

        $users = $query->select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->limit(20)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->full_name . ' (' . $user->email . ')'
                ];
            });

        return response()->json($users);
    }

    /**
     * Get user statistics.
     */
    public function statistics()
    {
        $this->authorize('viewStatistics', User::class);

        $stats = [
            'total' => User::count(),
            'active' => User::whereNotNull('email_verified_at')->count(),
            'inactive' => User::whereNull('email_verified_at')->count(),
            'by_division' => User::with('division')
                ->select('division_id', DB::raw('count(*) as count'))
                ->groupBy('division_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->division?->name ?? 'Unassigned' => $item->count];
                }),
            'by_cluster' => User::with('cluster')
                ->select('cluster_id', DB::raw('count(*) as count'))
                ->groupBy('cluster_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->cluster?->name ?? 'Unassigned' => $item->count];
                }),
            'by_role' => DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(*) as count'))
                ->groupBy('roles.name')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->name => $item->count];
                }),
            'recent' => User::latest()->take(5)->get(),
        ];

        return response()->json($stats);
    }
}