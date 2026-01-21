<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\IssueType;
use Illuminate\Http\Request;

class BaseDataController extends Controller
{
    /**
     * Display the base data dashboard.
     */
    public function index()
    {
        // Get counts for statistics
        $itemCount = Item::count();
        $issueTypeCount = IssueType::count();
        
        // Define the base data modules
        $modules = [
            [
                'title' => 'Items',
                'description' => 'Manage all items in the system',
                'icon' => 'bi-box',
                'count' => $itemCount,
                'route' => 'items.index',
                'color' => 'blue',
                'bg_color' => 'bg-blue-50 dark:bg-blue-900/20',
                'border_color' => 'border-blue-200 dark:border-blue-800',
                'text_color' => 'text-blue-600 dark:text-blue-400',
            ],
            [
                'title' => 'Issue Types',
                'description' => 'Manage different types of maintenance issues',
                'icon' => 'bi-tag',
                'count' => $issueTypeCount,
                'route' => 'issue-types.index',
                'color' => 'green',
                'bg_color' => 'bg-green-50 dark:bg-green-900/20',
                'border_color' => 'border-green-200 dark:border-green-800',
                'text_color' => 'text-green-600 dark:text-green-400',
            ],
            // You can add more modules here in the future
            // [
            //     'title' => 'Categories',
            //     'description' => 'Manage item categories',
            //     'icon' => 'bi-folder',
            //     'count' => 0,
            //     'route' => 'categories.index',
            //     'color' => 'purple',
            // ],
        ];
        
        return view('basEdata.index', compact('modules'));
    }
}