<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // LOGIC: Super Admin sees everyone. Regular Admin sees only themselves.
        if (Auth::user()->role === 'super_admin') {
            // 1. Prioritize 'super_admin' (Value 1) over everyone else (Value 2)
            // 2. Then sort by 'created_at' descending (newest users first)
            $users = User::orderByRaw("CASE WHEN role = 'super_admin' THEN 1 ELSE 2 END ASC")
                        ->orderBy('created_at', 'desc') 
                        ->paginate(10);
        } else {
            $users = User::where('id', Auth::id())->paginate(10);
        }
        
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        // SECURITY: Only Super Admin can create new users
        if (Auth::user()->role !== 'super_admin') {
            return back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:super_admin,admin', // Strictly enforce roles
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        // 📝 LOG: User Creation
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'User Created',
            'details'     => "Created user '{$user->name}' with role '{$user->role}'."
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user)
    {
        // 1. Check Permissions (Can only edit self unless Super Admin)
        if (Auth::user()->role !== 'super_admin' && Auth::user()->id !== $user->id) {
            return back()->with('error', 'You can only edit your own account.')->withInput([
                'edit_form' => 1,
                'user_id' => $user->id,
            ]);
        }

        // 2. If Editing Self (Security Check Required)
        if (Auth::id() === $user->id) {
            // Verify Old Password is present and correct
            $request->validate([
                'current_password' => 'required',
            ], [
                'current_password.required' => 'You must enter your current password to make changes.'
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                    ->withInput(array_merge($request->all(), [
                        'edit_form' => 1,
                        'user_id' => $user->id,
                    ]));
            }
        }

        // 3. Validate New Data
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'new_password' => 'nullable|string|min:8|confirmed', 
        ], [
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
        ]);

        // 4. Update Basic Info
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // 5. Role Logic (Only Super Admin can change roles)
        if (Auth::user()->role === 'super_admin' && $request->has('role')) {
            $user->role = $request->role;
        }

        // 6. Update Password (If provided)
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // 7. Log Action
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'User Updated',
            'details'     => "Updated profile for user '{$user->name}'."
        ]);

        return redirect()->route('users.index')->with('success', 'Profile updated successfully!');
    }

    public function destroy(User $user)
    {
        // SECURITY: Only Super Admin can delete
        if (Auth::user()->role !== 'super_admin') {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($user->id == 1) {
            return back()->with('error', 'Cannot delete the Main Super Admin!');
        }
        
        // Prevent deleting yourself
        if ($user->id == Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $userName = $user->name;
        $user->delete();

        // 📝 LOG: User Deletion
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'User Deleted',
            'details'     => "Deleted user account '{$userName}'."
        ]);

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}