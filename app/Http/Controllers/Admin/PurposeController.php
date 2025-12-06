<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PurposeRequest;
use App\Models\Purpose;
use Illuminate\Http\Request;

class PurposeController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view('admin.purposes.index');
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

        $purposes = Purpose::get($where, null, ['id', 'desc'], null, null);

        return $this->jsonSuccess('Purposes retrieved', $purposes);
    }

    public function create()
    {
        return view('admin.purposes.create');
    }

    public function ajaxAdd()
    {
        return view('admin.purposes.ajax_add');
    }

    public function ajaxEdit(Purpose $purpose)
    {
        return view('admin.purposes.ajax_edit', compact('purpose'));
    }

    public function store(PurposeRequest $request)
    {
        $purpose = Purpose::create($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Purpose created successfully', $purpose);
        }

        return redirect()->route('admin.purposes.index')->with('success', 'Purpose created successfully.');
    }

    public function show(Purpose $purpose)
    {
        return view('admin.purposes.show', compact('purpose'));
    }

    public function edit(Purpose $purpose)
    {
        return view('admin.purposes.edit', compact('purpose'));
    }

    public function update(PurposeRequest $request, Purpose $purpose)
    {
        $purpose->update($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Purpose updated successfully', $purpose);
        }

        return redirect()->route('admin.purposes.index')->with('success', 'Purpose updated successfully.');
    }

    public function destroy(Purpose $purpose)
    {
        $purpose->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Purpose deleted successfully');
        }

        return redirect()->route('admin.purposes.index')->with('success', 'Purpose deleted successfully.');
    }

    public function toggleStatus(Purpose $purpose)
    {
        $purpose->status = $purpose->status === 'active' ? 'inactive' : 'active';
        $purpose->save();

        if (request()->ajax()) {
            return $this->jsonSuccess('Status updated successfully', $purpose);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}

