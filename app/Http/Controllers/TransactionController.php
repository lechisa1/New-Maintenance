<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class TransactionController extends Controller
{
public function index()
{
            // Fetch users from database
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'image' => '/images/user/user-01.png', // Default image
                'date' => $user->created_at->format('M d, Y h:i A'),
                'status' => $user->id % 2 === 0 ? 'Active' : 'Inactive', // Example status
                'role' => $user->id === 1 ? 'Admin' : 'User', // Example role
            ];
        })->toArray();

    $transactions = [
        [
            'id' => 1,
            'name' => 'Bought PYPL',
            'image' => '/images/brand/brand-08.svg',
            'date' => 'Nov 23, 01:00 PM',
            'price' => '$2,567.88',
            'category' => 'Finance',
            'status' => 'Success',
        ],
        [
            'id' => 2,
            'name' => 'Bought AAPL',
            'image' => '/images/brand/brand-07.svg',
            'date' => 'Nov 23, 01:00 PM',
            'price' => '$2,567.88',
            'category' => 'Finance',
            'status' => 'Pending',
        ],
        [
            'id' => 3,
            'name' => 'Bought GOOGL',
            'image' => '/images/brand/brand-09.svg',
            'date' => 'Nov 22, 02:30 PM',
            'price' => '$3,450.00',
            'category' => 'Technology',
            'status' => 'Failed',
        ],
        [
            'id' => 4,
            'name' => 'Bought MSFT',
            'image' => '/images/brand/brand-10.svg',
            'date' => 'Nov 21, 11:15 AM',
            'price' => '$1,890.50',
            'category' => 'Technology',
            'status' => 'Success',
        ],
        [
            'id' => 5,
            'name' => 'Bought TSLA',
            'image' => '/images/brand/brand-11.svg',
            'date' => 'Nov 20, 09:45 AM',
            'price' => '$2,150.75',
            'category' => 'Automotive',
            'status' => 'Pending',
        ],
    ];

           return view('admin.transactions', [
            'transactions' => $transactions,
            'users' => $users, // Add users to the view
        ]);
}
}
