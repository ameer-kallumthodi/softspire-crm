<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\LeadStatusRequest;
use App\Models\LeadStatus;
use Illuminate\Http\Request;

class LeadStatusController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view('admin.lead-statuses.index');
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

        $leadStatuses = LeadStatus::get($where, null, ['id', 'desc'], null, null);

        return $this->jsonSuccess('Lead statuses retrieved', $leadStatuses);
    }

    public function create()
    {
        return view('admin.lead-statuses.create');
    }

    public function ajaxAdd()
    {
        return view('admin.lead-statuses.ajax_add');
    }

    public function ajaxEdit(LeadStatus $leadStatus)
    {
        return view('admin.lead-statuses.ajax_edit', compact('leadStatus'));
    }

    public function store(LeadStatusRequest $request)
    {
        $leadStatus = LeadStatus::create($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead status created successfully', $leadStatus);
        }

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Lead status created successfully.');
    }

    public function show(LeadStatus $leadStatus)
    {
        return view('admin.lead-statuses.show', compact('leadStatus'));
    }

    public function edit(LeadStatus $leadStatus)
    {
        return view('admin.lead-statuses.edit', compact('leadStatus'));
    }

    public function update(LeadStatusRequest $request, LeadStatus $leadStatus)
    {
        $leadStatus->update($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead status updated successfully', $leadStatus);
        }

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Lead status updated successfully.');
    }

    public function destroy(LeadStatus $leadStatus)
    {
        $leadStatus->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Lead status deleted successfully');
        }

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Lead status deleted successfully.');
    }

    public function toggleStatus(LeadStatus $leadStatus)
    {
        $leadStatus->status = $leadStatus->status === 'active' ? 'inactive' : 'active';
        $leadStatus->save();

        if (request()->ajax()) {
            return $this->jsonSuccess('Status updated successfully', $leadStatus);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}

