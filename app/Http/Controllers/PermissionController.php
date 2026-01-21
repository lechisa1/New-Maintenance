<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
public function index(Request $request)
{
    $query = Permission::query();

    // 1. Filter by Search (checks resource name or full permission name)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('resource', 'like', "%{$search}%");
        });
    }

    // 2. Filter by Status
    if ($request->filled('status')) {
        $status = $request->status === 'active' ? true : false;
        $query->where('is_active', $status);
    }

    $allPermissions = $query->get();
    
    // Group and extract actions for the matrix headers
    $grouped = $allPermissions->groupBy('resource');
    
    $actions = Permission::all()->map(function($p) {
        return strpos($p->name, '.') !== false ? explode('.', $p->name)[1] : $p->name;
    })->unique()->sort();

    return view('permissions.index', compact('grouped', 'actions'));
}

    public function toggleStatus(Permission $permission)
    {
        $permission->is_active = !$permission->is_active;
        $permission->save();

        return back()->with('success', "Permission '{$permission->name}' status updated.");
    }
}