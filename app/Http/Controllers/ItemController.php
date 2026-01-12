<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::query();
        
        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get items with filters
        $items = $query->latest()->paginate(5);
        
        // Statistics
        $totalItems = Item::count();
        $activeItems = Item::active()->count();
        $inactiveItems = Item::inactive()->count();
        $maintenanceItems = Item::maintenance()->count();
        
        // Count by type
        $typeCounts = [];
        foreach (Item::getTypeOptions() as $key => $value) {
            $typeCounts[$key] = Item::byType($key)->count();
        }
        
        return view('items.index', compact(
            'items',
            'totalItems',
            'activeItems',
            'inactiveItems',
            'maintenanceItems',
            'typeCounts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        try {
            //  dd($request->all());
           Item::create($request->validated());
           
            return redirect()->route('items.index')
                ->with('success', 'Equipment registered successfully!');
                
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to register equipment. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        try {
            $item->update($request->validated());
            
            return redirect()->route('items.index')
                ->with('success', 'Equipment updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update equipment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            $item->delete();
            
            return redirect()->route('items.index')
                ->with('success', 'Equipment deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete equipment. Please try again.');
        }
    }

    /**
     * Restore soft deleted item
     */
    public function restore($id)
    {
        try {
            $item = Item::withTrashed()->findOrFail($id);
            $item->restore();
            
            return redirect()->route('items.index')
                ->with('success', 'Equipment restored successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore equipment. Please try again.');
        }
    }

    /**
     * Permanently delete item
     */
    public function forceDelete($id)
    {
        try {
            $item = Item::withTrashed()->findOrFail($id);
            $item->forceDelete();
            
            return redirect()->route('items.trashed')
                ->with('success', 'Equipment permanently deleted!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to permanently delete equipment.');
        }
    }

    /**
     * Show trashed items
     */
    public function trashed()
    {
        $items = Item::onlyTrashed()->latest()->paginate(15);
        return view('items.trashed', compact('items'));
    }

    /**
     * Export items to CSV
     */
    public function export()
    {
        $items = Item::latest()->get();
        $filename = 'equipment_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, ['ID', 'Name', 'Type', 'Unit', 'Status', 'Created At', 'Last Updated']);
            
            // Data
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->getTypeText(),
                    $item->getUnitText(),
                    $item->getStatusText(),
                    $item->created_at->format('Y-m-d H:i:s'),
                    $item->updated_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}