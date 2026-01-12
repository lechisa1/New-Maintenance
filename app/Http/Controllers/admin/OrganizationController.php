<?php

namespace App\Http\Controllers\admin;

use App\Models\Organization;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load organizations with their clusters count
        $organizations = Organization::withCount('clusters')
            ->latest()
            ->paginate(10);
        
        // Get users for chairman selection
        $users = User::select('id', 'name')->get();
        
        return view('organizations.index', compact('organizations', 'users'));
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
            'id' => Str::uuid(),
            'name' => $validated['name']
        ]);

        return response()->json($organization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $organization->id . ',id',
        ]);

        $organization->update($validated);

        return response()->json($organization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();
        return response()->json(['message' => 'Organization deleted successfully']);
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
}