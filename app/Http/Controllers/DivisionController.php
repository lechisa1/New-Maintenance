<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DivisionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
            'cluster_id' => 'required|exists:clusters,id',
            'division_chairman' => 'nullable|exists:users,id'
        ]);

        $division = Division::create([
            'id' => Str::uuid(),
            'name' => $validated['name'],
            'cluster_id' => $validated['cluster_id'],
            'division_chairman' => $validated['division_chairman']
        ]);

        return response()->json($division->load(['cluster:id,name', 'chairman:id,name']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id . ',id',
            'cluster_id' => 'required|exists:clusters,id',
            'division_chairman' => 'nullable|exists:users,id'
        ]);

        $division->update($validated);

        return response()->json($division->load(['cluster:id,name', 'chairman:id,name']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        $division->delete();
        return response()->json(['message' => 'Division deleted successfully']);
    }
}