<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Organization;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\User;

use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $search = $request->query('search'); // Get search term from query string

    $query = Organization::withCount('clusters')
        ->orderByDesc('created_at');

    // Apply search if present
    if ($search) {
        $query->where('name', 'like', "%{$search}%");
    }

    // Paginate results
    $paginator = $query->paginate(5)->withQueryString(); // keep search in pagination links

    return view('organizations.index', [
        'organizations' => $paginator,
    ]);
}



    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:organizations,name',
    ]);

    $organization = Organization::create([
        'id' => (string) Str::uuid(), // Ensure it's cast to string
        'name' => $validated['name']
    ]);

    return response()->json([
        'message' => 'Organization created successfully',
        'data' => $organization
    ], 201);
}

    /**
     * Update the specified resource in storage.
     */
// Update Organization
public function update(Request $request, Organization $organization)
{
    $validated = $request->validate([
        // Unique check ignores the current record ID
        'name' => 'required|string|max:255|unique:organizations,name,' . $organization->id,
    ]);

    $organization->update($validated);

    return response()->json([
        'message' => 'Organization updated successfully',
        'data' => $organization
    ]);
}

// Delete Organization
public function destroy(Organization $organization)
{
    $organization->delete();

    return response()->json([
        'message' => 'Organization deleted successfully'
    ]);
}

    /**
     * Get clusters for an organization
     */
    public function getClusters($organizationId)
    {
        $clusters = Cluster::where('organization_id', $organizationId)
            ->with(['chairman:id,name', 'divisions:id,cluster_id'])
            ->withCount('divisions')
            ->get()
            ->map(function($cluster) {
                return [
                    'id' => $cluster->id,
                    'name' => $cluster->name,
                    'chairman' => $cluster->chairman,
                    'divisions_count' => $cluster->divisions_count,
                    'created_at' => $cluster->created_at,
                    'updated_at' => $cluster->updated_at
                ];
            });
        
        return response()->json($clusters);
    }

    /**
     * Get divisions for an organization
     */
    public function getDivisions($organizationId)
    {
        $divisions = Division::whereHas('cluster', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->with(['cluster:id,name', 'chairman:id,name'])
            ->get()
            ->map(function($division) {
                return [
                    'id' => $division->id,
                    'name' => $division->name,
                    'cluster_id' => $division->cluster_id,
                    'cluster_name' => $division->cluster->name,
                    'chairman' => $division->chairman,
                    'created_at' => $division->created_at,
                    'updated_at' => $division->updated_at
                ];
            });
        
        return response()->json($divisions);
    }
public function clusters($organizationId)
{
    return response()->json(
        Cluster::where('organization_id', $organizationId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
    );
}
    public function show(Organization $organization)
{
    $organization->loadCount('clusters');

    $clusters = $organization->clusters()
        ->withCount('divisions')
        ->latest()
        ->paginate(10);
        $users = \App\Models\User::select('id', 'full_name')->orderBy('full_name')->get();

    return view('organizations.show', compact('organization', 'clusters', 'users'));
}


}