<?php

namespace App\Http\Controllers\director;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    //
    public function itemIndex()
    {
        $items = Item::with('categories')->latest()->get();
        $pageTitle = 'Item List'; 
        return view('Item.item_index', compact('items','pageTitle'));
    }
    public function itemRegisterationForm()
    {
        $categories = Category::all();
        if (!$categories->contains('name', 'Unknown Cause')) {
            $categories->push((object)['id' => 'unknown', 'name' => 'Unknown Cause']);
        }
        return view('Item.item_registeration_form', compact('categories'));
    }
    // public function itemRegisteration(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => "required|unique:items,name|string",
    //         'unit' => "nullable|string",
    //         'in_stock' => "nullable|integer",
    //         'categories' => "nullable|array",
    //         'categories.*' => "exists:categories,id",
    //     ]);
    //     try {
    //         DB::beginTransaction();

    //         $item = Item::create([
    //             'name' => $validated['name'],
    //             'unit' => $validated['unit'],
    //             'in_stock' => $validated['in_stock'] ?? 0,
    //         ]);

    //         if (!empty($validated['categories'])) {
    //             $item->categories()->sync($validated['categories']);
    //         }

    //         DB::commit();

    //         return redirect()->route('item_index')
    //             ->with("success", "Item registered successfully");
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withInput()
    //             ->with("error", "Failed to register item: " . $e->getMessage());
    //     }
    // }
    public function itemRegisteration(Request $request)
{
    $validated = $request->validate([
        'name' => "required|unique:items,name|string",
        'unit' => "nullable|string",
        'in_stock' => "nullable|integer",
        'categories' => "nullable|array",
    ]);

    try {
        DB::beginTransaction();

        $item = Item::create([
            'name' => $validated['name'],
            'unit' => $validated['unit'],
            'in_stock' => $validated['in_stock'] ?? 0,
        ]);

        $categoryIds = [];

        if (!empty($validated['categories'])) {
            foreach ($validated['categories'] as $cat) {
                if ($cat === 'unknown') {
                    $unknown = Category::firstOrCreate(['name' => 'Unknown Cause']);
                    $categoryIds[] = $unknown->id;
                } else {
                    $categoryIds[] = (int) $cat;
                }
            }

            $item->categories()->sync($categoryIds);
        }

        DB::commit();

        return redirect()->route('item_index')
            ->with("success", "Item registered successfully");

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with("error", "Failed to register item: " . $e->getMessage());
    }
}
    public function getCategory($itemId)
    {
        $item = Item::with('categories')->find($itemId);

        if (!$item || $item->categories->isEmpty()) {
            return response()->json(['category_id' => null]);
        }

        // Return first related category (or all if needed)
        return response()->json([
            'category_id' => $item->categories->first()->id,
        ]);
    }
    public function editItemForm($id)
    {
        $item = Item::with('categories')->findOrFail($id);
        $categories = Category::all();
        return view('Item.edit_item', compact('item', 'categories'));
    }

    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => "required|string|max:255|unique:items,name,$id",
            'unit' => "nullable|string|max:50",
            'in_stock' => "nullable|integer|min:0",
            'categories' => "nullable|array",
            'categories.*' => "exists:categories,id",
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);
            $item->update([
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'in_stock' => $validated['in_stock'] ?? 0,
            ]);

            $item->categories()->sync($validated['categories'] ?? []);

            DB::commit();

            return redirect()->route('item_index')
                ->with("success", "Item updated successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with("error", "Failed to update item: " . $e->getMessage());
        }
    }
    public function destroyItem(Item $item)
    {
        $item->delete();
        return redirect()->route('item_index')->with('success', "Item Deleted Successfully");
    }
}
