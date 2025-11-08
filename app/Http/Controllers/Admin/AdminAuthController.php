<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = \App\Models\AdminUser::where('email', $request->email)
            ->where('status', 1)
            ->first();

        if (!$admin || !\Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'Invalid credentials');
        }

        // Store admin session
        session([
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
            'admin_role' => $admin->role,
        ]);

        // Update last login
        $admin->update(['last_login' => now()]);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_email', 'admin_role']);
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    /**
     * Show profile
     */
    public function showProfile()
    {
        $admin = \App\Models\AdminUser::find(session('admin_id'));
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:admin_users,email,' . session('admin_id'),
            'password' => 'nullable|min:6|confirmed',
        ]);

        $admin = \App\Models\AdminUser::find(session('admin_id'));

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = bcrypt($request->password);
        }

        $admin->save();

        return back()->with('success', 'Profile updated successfully');
    }

    /**
     * Show settings
     */
    public function showSettings()
    {
        return view('admin.settings');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        // Settings logic here
        return back()->with('success', 'Settings updated successfully');
    }

    /**
     * List roles
     */
    public function listRoles()
    {
        return view('admin.roles.list');
    }

    /**
     * Create role
     */
    public function createRole()
    {
        return view('admin.roles.create');
    }

    /**
     * Store role
     */
    public function storeRole(Request $request)
    {
        return redirect()->route('admin.roles.list')->with('success', 'Role created successfully');
    }

    /**
     * Edit role
     */
    public function editRole($id)
    {
        return view('admin.roles.edit', compact('id'));
    }

    /**
     * Update role
     */
    public function updateRole(Request $request, $id)
    {
        return redirect()->route('admin.roles.list')->with('success', 'Role updated successfully');
    }

    /**
     * Delete role
     */
    public function deleteRole($id)
    {
        return redirect()->route('admin.roles.list')->with('success', 'Role deleted successfully');
    }
}
