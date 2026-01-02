<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Department;
use App\Helpers\CountriesHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends BaseController
{
    /**
     * Generate unique employee ID (ST0001, ST0002, etc.)
     */
    private function generateEmployeeId()
    {
        // Get the last employee ID
        $lastEmployee = User::whereNotNull('employee_id')
            ->where('employee_id', 'like', 'ST%')
            ->orderBy(DB::raw('CAST(SUBSTRING(employee_id, 3) AS UNSIGNED)'), 'desc')
            ->first();

        if ($lastEmployee) {
            // Extract the number part
            $lastNumber = (int) substr($lastEmployee->employee_id, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format as ST0001, ST0002, etc.
        return 'ST' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display listing of employees
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }

        return view('admin.employees.index');
    }

    private function getData(Request $request)
    {
        $where = [];
        $whereOR = [];

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $whereOR['name'] = ['like', "%{$search}%"];
            $whereOR['email'] = ['like', "%{$search}%"];
            $whereOR['employee_id'] = ['like', "%{$search}%"];
            $where['OR'] = $whereOR;
        }

        // Get employee role
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole) {
            $where['role_id'] = $employeeRole->id;
        } else {
            // If role doesn't exist, return empty
            return $this->jsonSuccess('Employees retrieved', []);
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
        
        $employees = $query->with(['role', 'department'])->orderBy('id', 'desc')->get();

        return $this->jsonSuccess('Employees retrieved', $employees);
    }

    /**
     * Store a newly created employee
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
            'department_id' => 'required|exists:departments,id',
            'joining_date' => 'required|date',
            'dob' => 'required|date|before:' . now()->subYears(15)->format('Y-m-d'),
            'address' => 'nullable|string|max:500',
        ]);

        // Get employee role
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if (!$employeeRole) {
            if ($request->ajax()) {
                return $this->jsonError('Employee role not found', 404);
            }
            return redirect()->back()->with('error', 'Employee role not found.');
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = $employeeRole->id;
        $data['employee_id'] = $this->generateEmployeeId();

        $user = User::create($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Employee created successfully', $user);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'status' => 'required|string|in:active,inactive',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'joining_date' => 'required|date',
            'dob' => 'required|date|before:' . now()->subYears(15)->format('Y-m-d'),
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($id);
        
        // Verify user is an employee
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole && $user->role_id != $employeeRole->id) {
            if ($request->ajax()) {
                return $this->jsonError('User is not an employee', 403);
            }
            return redirect()->back()->with('error', 'User is not an employee.');
        }
        
        $data = $request->all();
        
        // Keep the existing role_id and employee_id (don't allow changing)
        $data['role_id'] = $user->role_id;
        $data['employee_id'] = $user->employee_id;

        $user->update($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Employee updated successfully', $user);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Verify user is an employee
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole && $user->role_id != $employeeRole->id) {
            if (request()->ajax()) {
                return $this->jsonError('User is not an employee', 403);
            }
            return redirect()->back()->with('error', 'User is not an employee.');
        }
        
        if ($user->id == auth()->id()) {
            if (request()->ajax()) {
                return $this->jsonError('You cannot delete your own account', 400);
            }
            return redirect()->route('admin.employees.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Employee deleted successfully');
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }

    /**
     * Get employee data for edit
     */
    public function show($id)
    {
        $user = User::with(['role', 'department'])->findOrFail($id);
        
        // Verify user is an employee
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole && $user->role_id != $employeeRole->id) {
            return $this->jsonError('User is not an employee', 403);
        }
        
        return $this->jsonSuccess('Employee retrieved', $user);
    }

    /**
     * Get AJAX add form
     */
    public function ajaxAdd(Request $request)
    {
        $countryCodes = CountriesHelper::getCountryCode();
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        return view('admin.employees.ajax_add', compact('countryCodes', 'departments'));
    }

    /**
     * Get AJAX edit form
     */
    public function ajaxEdit($id)
    {
        $user = User::with(['role', 'department'])->findOrFail($id);
        
        // Verify user is an employee
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole && $user->role_id != $employeeRole->id) {
            if (request()->ajax()) {
                return $this->jsonError('User is not an employee', 403);
            }
            return redirect()->route('admin.employees.index')->with('error', 'User is not an employee.');
        }
        
        $countryCodes = CountriesHelper::getCountryCode();
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        return view('admin.employees.ajax_edit', compact('user', 'countryCodes', 'departments'));
    }

    /**
     * Show reset password form
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Verify user is an employee
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole && $user->role_id != $employeeRole->id) {
            if (request()->ajax()) {
                return $this->jsonError('User is not an employee', 403);
            }
            return redirect()->route('admin.employees.index')->with('error', 'User is not an employee.');
        }
        
        return view('admin.employees.reset_password', compact('user'));
    }

    /**
     * Update employee password
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        
        // Verify user is an employee
        $employeeRole = UserRole::get(['slug' => 'employee'], null, null, 1, null)->first();
        if ($employeeRole && $user->role_id != $employeeRole->id) {
            if ($request->ajax()) {
                return $this->jsonError('User is not an employee', 403);
            }
            return redirect()->back()->with('error', 'User is not an employee.');
        }
        
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->ajax()) {
            return $this->jsonSuccess('Password reset successfully');
        }

        return redirect()->route('admin.employees.index')->with('success', 'Password reset successfully.');
    }
}
