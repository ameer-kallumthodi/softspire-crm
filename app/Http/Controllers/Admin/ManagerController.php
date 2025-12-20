<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\UserRole;
use App\Helpers\CountriesHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends BaseController
{
    /**
     * Display listing of managers
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }

        return view('admin.managers.index');
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

        // Get manager role
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole) {
            $where['role_id'] = $managerRole->id;
        } else {
            // If role doesn't exist, return empty
            return $this->jsonSuccess('Managers retrieved', []);
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
        
        $managers = $query->with('role')->orderBy('id', 'desc')->get();

        return $this->jsonSuccess('Managers retrieved', $managers);
    }

    /**
     * Store a newly created manager
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|string|in:active,inactive',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'joining_date' => 'required|date',
            'dob' => 'required|date|before:' . now()->format('Y-m-d'),
        ]);

        // Get manager role
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if (!$managerRole) {
            if ($request->ajax()) {
                return $this->jsonError('Manager role not found', 404);
            }
            return redirect()->back()->with('error', 'Manager role not found.');
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = $managerRole->id;

        $user = User::create($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Manager created successfully', $user);
        }

        return redirect()->route('admin.managers.index')->with('success', 'Manager created successfully.');
    }

    /**
     * Update the specified manager
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'status' => 'required|string|in:active,inactive',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'joining_date' => 'required|date',
            'dob' => 'required|date|before:' . now()->format('Y-m-d'),
        ]);

        $user = User::findOrFail($id);
        
        // Verify user is a manager
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole && $user->role_id != $managerRole->id) {
            if ($request->ajax()) {
                return $this->jsonError('User is not a manager', 403);
            }
            return redirect()->back()->with('error', 'User is not a manager.');
        }
        
        $data = $request->all();
        
        // Keep the existing role_id (don't allow changing role)
        $data['role_id'] = $user->role_id;

        $user->update($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Manager updated successfully', $user);
        }

        return redirect()->route('admin.managers.index')->with('success', 'Manager updated successfully.');
    }

    /**
     * Remove the specified manager
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Verify user is a manager
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole && $user->role_id != $managerRole->id) {
            if (request()->ajax()) {
                return $this->jsonError('User is not a manager', 403);
            }
            return redirect()->back()->with('error', 'User is not a manager.');
        }
        
        if ($user->id == auth()->id()) {
            if (request()->ajax()) {
                return $this->jsonError('You cannot delete your own account', 400);
            }
            return redirect()->route('admin.managers.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Manager deleted successfully');
        }

        return redirect()->route('admin.managers.index')->with('success', 'Manager deleted successfully.');
    }

    /**
     * Get manager data for edit
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        
        // Verify user is a manager
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole && $user->role_id != $managerRole->id) {
            return $this->jsonError('User is not a manager', 403);
        }
        
        return $this->jsonSuccess('Manager retrieved', $user);
    }

    /**
     * Get AJAX add form
     */
    public function ajaxAdd(Request $request)
    {
        $countryCodes = CountriesHelper::getCountryCode();
        return view('admin.managers.ajax_add', compact('countryCodes'));
    }

    /**
     * Get AJAX edit form
     */
    public function ajaxEdit($id)
    {
        $user = User::with('role')->findOrFail($id);
        
        // Verify user is a manager
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole && $user->role_id != $managerRole->id) {
            if (request()->ajax()) {
                return $this->jsonError('User is not a manager', 403);
            }
            return redirect()->route('admin.managers.index')->with('error', 'User is not a manager.');
        }
        
        $countryCodes = CountriesHelper::getCountryCode();
        return view('admin.managers.ajax_edit', compact('user', 'countryCodes'));
    }

    /**
     * Show reset password form
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Verify user is a manager
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole && $user->role_id != $managerRole->id) {
            if (request()->ajax()) {
                return $this->jsonError('User is not a manager', 403);
            }
            return redirect()->route('admin.managers.index')->with('error', 'User is not a manager.');
        }
        
        return view('admin.managers.reset_password', compact('user'));
    }

    /**
     * Update manager password
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        
        // Verify user is a manager
        $managerRole = UserRole::get(['slug' => 'manager'], null, null, 1, null)->first();
        if ($managerRole && $user->role_id != $managerRole->id) {
            if ($request->ajax()) {
                return $this->jsonError('User is not a manager', 403);
            }
            return redirect()->back()->with('error', 'User is not a manager.');
        }
        
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->ajax()) {
            return $this->jsonSuccess('Password reset successfully');
        }

        return redirect()->route('admin.managers.index')->with('success', 'Password reset successfully.');
    }
}
