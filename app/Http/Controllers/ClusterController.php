<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Division;
class ClusterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
// In ClusterController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:clusters,name',
        'organization_id' => 'required|exists:organizations,id',
        'cluster_chairman' => 'nullable|exists:users,id' // This is fine
    ]);

    $cluster = Cluster::create([
        'id' => (string) Str::uuid(),
        'name' => $validated['name'],
        'organization_id' => $validated['organization_id'],
        'cluster_chairman' => $request->input('cluster_chairman') // Use input() to avoid array key errors
    ]);

    return response()->json($cluster->load('chairman:id,full_name'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cluster $cluster)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clusters,name,' . $cluster->id . ',id',
            'cluster_chairman' => 'nullable|exists:users,id'
        ]);

        $cluster->update($validated);

        return response()->json($cluster->load('chairman:id,full_name'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cluster $cluster)
    {
        $cluster->delete();
        return response()->json(['message' => 'Cluster deleted successfully']);
    }
public function divisions($clusterId)
{
    return response()->json(
        Division::where('cluster_id', $clusterId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
    );
}
public function showDivisions(Request $request, Cluster $cluster)
{
    $cluster->loadCount('divisions');

    $query = $cluster->divisions()
        ->with('chairman')
        ->orderBy('name');

    // Add search logic
    if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('chairman', function($userQuery) use ($search) {
                  $userQuery->where('full_name', 'like', "%{$search}%");
              });
        });
    }

    $divisions = $query->paginate(10)->withQueryString();

    $users = \App\Models\User::select('id', 'full_name')->orderBy('full_name')->get();

    return view('organizations.clusters.show', compact('cluster', 'divisions', 'users'));
}


}