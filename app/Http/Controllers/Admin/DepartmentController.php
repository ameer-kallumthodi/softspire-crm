<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view('admin.departments.index');
    }

    private function getData(Request $request)
    {
        $where = [];
        
        if ($request->has('search') && $request->search != '') {
            $where['name'] = ['like', "%{$request->search}%"];
        }

        if ($request->has('status') && $request->status != '') {
            $where['status'] = $request->status;
        }

        $departments = Department::get($where, null, ['id', 'desc'], null, null);

        return $this->jsonSuccess('Departments retrieved', $departments);
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function ajaxAdd()
    {
        return view('admin.departments.ajax_add');
    }

    public function ajaxEdit(Department $department)
    {
        return view('admin.departments.ajax_edit', compact('department'));
    }

    public function store(DepartmentRequest $request)
    {
        $department = Department::create($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Department created successfully', $department);
        }

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Department updated successfully', $department);
        }

        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Department deleted successfully');
        }

        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }

    public function toggleStatus(Department $department)
    {
        $department->status = $department->status === 'active' ? 'inactive' : 'active';
        $department->save();

        if (request()->ajax()) {
            return $this->jsonSuccess('Status updated successfully', $department);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
