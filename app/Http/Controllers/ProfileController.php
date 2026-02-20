<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = Auth::user();

    // Delete old avatar if exists
    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Store new avatar
    $path = $request->file('avatar')
        ->store('avatars', 'public');

    $user->update([
        'avatar' => $path
    ]);

    return back()->with('success', 'Avatar updated successfully.');
}


public function index()
{
    $user = Auth::user();

    $totalOrders = $user->orders()->count();
    $wishlistCount = $user->wishlist()->count();
    $reviewCount = $user->reviews()->count();

    $completion = 0;

    if ($user->name) $completion += 25;
    if ($user->email) $completion += 25;
    if ($user->avatar) $completion += 25;
    if ($user->cover) $completion += 25;


    // Get recent purchased books
    $recentBooks = \App\Models\OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with('book')
        ->latest()
        ->take(7)
        ->get();

    return view('profile.index', compact(
        'user',
        'totalOrders',
        'wishlistCount',
        'reviewCount',
        'completion',
        'recentBooks'
    ));
}



    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }

public function updateCover(Request $request)
{
    $request->validate([
        'cover' => 'required|image|mimes:jpg,jpeg,png|max:4096',
    ]);

    $user = Auth::user();

    // Delete old cover
    if ($user->cover && Storage::disk('public')->exists($user->cover)) {
        Storage::disk('public')->delete($user->cover);
    }

    // Store new cover
    $path = $request->file('cover')
        ->store('covers', 'public');

    $user->update([
        'cover' => $path
    ]);

    return back()->with('success', 'Cover updated successfully.');
}


    public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6|confirmed'
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
    ];

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return back()->with('success', 'Profile updated successfully.');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
