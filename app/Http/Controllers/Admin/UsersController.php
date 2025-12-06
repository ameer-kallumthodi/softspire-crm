<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends BaseController
{
    /**
     * Display listing of users
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        
        $roles = UserRole::get(null, null, null, null, null);

        return view('admin.users.index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'role_id', 'role']),
        ]);
    }

    private function getData(Request $request)
    {
        $where = [];
        $whereOR = [];

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $whereOR['name'] = ['like', "%{$search}%"];
            $whereOR['email'] = ['like', "%{$search}%"];
            $where['OR'] = $whereOR;
        }

        // Handle role filter by slug (manager or telecaller)
        if ($request->has('role') && $request->role != '') {
            $roleSlug = $request->role;
            $role = UserRole::get(['slug' => $roleSlug], null, null, 1, null)->first();
            if ($role) {
                $where['role_id'] = $role->id;
            }
        }

        if ($request->has('role_id') && $request->role_id != '') {
            $where['role_id'] = $request->role_id;
        }

        $query = User::query();
        if (!empty($where)) {
            if (isset($where['OR'])) {
                $orConditions = $where['OR'];
                unset($where['OR']);
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } else {
                        $query->where($column, $value);
                    }
                }
                $query->where(function($q) use ($orConditions) {
                    foreach ($orConditions as $column => $value) {
                        if (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                            $q->orWhere($column, 'like', $value[1]);
                        } else {
                            $q->orWhere($column, $value);
                        }
                    }
                });
            } else {
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } else {
                        $query->where($column, $value);
                    }
                }
            }
        }
        
        $users = $query->with('role')->orderBy('id', 'desc')->get();

        return $this->jsonSuccess('Users retrieved', $users);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Determine role based on role_filter or default to telecaller
        $roleFilter = $request->input('role_filter', '');
        $roleId = null;
        
        if ($roleFilter === 'manager') {
            $role = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
            $roleId = $role ? $role->id : 3; // Manager ID is 3
        } elseif ($roleFilter === 'telecaller') {
            $role = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
            $roleId = $role ? $role->id : 2; // Telecaller ID is 2
        } else {
            // Default to telecaller if no filter specified
            $role = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
            $roleId = $role ? $role->id : 2;
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = $roleId;
        unset($data['role_filter']); // Remove role_filter from data

        $user = User::create($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('User created successfully', $user);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'required|string|in:active,inactive',
        ]);

        $user = User::findOrFail($id);
        $data = $request->except(['password', 'password_confirmation', 'role_filter']);
        
        // Keep the existing role_id (don't allow changing role)
        $data['role_id'] = $user->role_id;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('User updated successfully', $user);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == auth()->id()) {
            if (request()->ajax()) {
                return $this->jsonError('You cannot delete your own account', 400);
            }
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('User deleted successfully');
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Get user data for edit
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return $this->jsonSuccess('User retrieved', $user);
    }

    /**
     * Get AJAX add form
     */
    public function ajaxAdd(Request $request)
    {
        $roles = UserRole::get(null, null, null, null, null);
        $roleFilter = $request->get('role', '');
        return view('admin.users.ajax_add', compact('roles', 'roleFilter'));
    }

    /**
     * Get AJAX edit form
     */
    public function ajaxEdit($id)
    {
        $user = User::with('role')->findOrFail($id);
        $roles = UserRole::get(null, null, null, null, null);
        return view('admin.users.ajax_edit', compact('user', 'roles'));
    }
}

