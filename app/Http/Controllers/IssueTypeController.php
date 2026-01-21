<?php

namespace App\Http\Controllers;

use App\Models\IssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IssueTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $issueTypes = IssueType::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($status === 'active', function ($query) {
                return $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            })
            ->when($status === 'need_approval', function ($query) {
                return $query->where('is_need_approval', true);
            })
            ->latest()
            ->paginate(3);

        return view('basedata.issue-types.index', compact('issueTypes', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('basedata.issue-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:issue_types,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_need_approval' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        IssueType::create($validated);

        return redirect()->route('issue-types.index')
            ->with('success', 'Issue type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IssueType $issueType)
    {
        return view('basedata.issue-types.show', compact('issueType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IssueType $issueType)
    {
        return view('basedata.issue-types.edit', compact('issueType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IssueType $issueType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('issue_types')->ignore($issueType->id)],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_need_approval' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $issueType->update($validated);

        return redirect()->route('issue-types.index')
            ->with('success', 'Issue type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IssueType $issueType)
    {
        $issueType->delete();

        return redirect()->route('issue-types.index')
            ->with('success', 'Issue type deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(IssueType $issueType)
    {
        $issueType->update(['is_active' => !$issueType->is_active]);

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Toggle approval requirement
     */
    public function toggleApproval(IssueType $issueType)
    {
        $issueType->update(['is_need_approval' => !$issueType->is_need_approval]);

        return back()->with('success', 'Approval requirement updated.');
    }
}