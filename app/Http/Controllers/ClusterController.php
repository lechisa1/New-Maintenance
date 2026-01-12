<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClusterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clusters,name',
            'organization_id' => 'required|exists:organizations,id',
            'cluster_chairman' => 'nullable|exists:users,id'
        ]);

        $cluster = Cluster::create([
            'id' => Str::uuid(),
            'name' => $validated['name'],
            'organization_id' => $validated['organization_id'],
            'cluster_chairman' => $validated['cluster_chairman']
        ]);

        return response()->json($cluster->load('chairman:id,name'));
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

        return response()->json($cluster->load('chairman:id,name'));
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


}