<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class PasswordChangeController extends Controller
{
public function editPassword(User $user)
    {
        abort_if(auth()->id() !== $user->id, 403);
        return view('auth.change-password');
    }

public function updatePassword(Request $request, User $user)
{
    abort_if(auth()->id() !== $user->id, 403);

    $validated = $request->validate([
        'current_password' => ['required'],
        'password' => [
            'required',
            'confirmed',
            Password::min(8)->letters()->numbers(),
        ],
    ]);

    if (!Hash::check($validated['current_password'], $user->password)) {
        return back()->withErrors([
            'current_password' => 'Current password is incorrect.',
        ]);
    }

    $user->update([
        'password' => Hash::make($validated['password']),
    ]);

    return back()->with('success', 'Password updated successfully.');
}
}