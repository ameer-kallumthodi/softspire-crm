<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\LeadSourceRequest;
use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view('admin.lead-sources.index');
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

        $leadSources = LeadSource::get($where, null, ['id', 'desc'], null, null);

        return $this->jsonSuccess('Lead sources retrieved', $leadSources);
    }

    public function create()
    {
        return view('admin.lead-sources.create');
    }

    public function ajaxAdd()
    {
        return view('admin.lead-sources.ajax_add');
    }

    public function ajaxEdit(LeadSource $leadSource)
    {
        return view('admin.lead-sources.ajax_edit', compact('leadSource'));
    }

    public function store(LeadSourceRequest $request)
    {
        $leadSource = LeadSource::create($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead source created successfully', $leadSource);
        }

        return redirect()->route('admin.lead-sources.index')->with('success', 'Lead source created successfully.');
    }

    public function show(LeadSource $leadSource)
    {
        return view('admin.lead-sources.show', compact('leadSource'));
    }

    public function edit(LeadSource $leadSource)
    {
        return view('admin.lead-sources.edit', compact('leadSource'));
    }

    public function update(LeadSourceRequest $request, LeadSource $leadSource)
    {
        $leadSource->update($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead source updated successfully', $leadSource);
        }

        return redirect()->route('admin.lead-sources.index')->with('success', 'Lead source updated successfully.');
    }

    public function destroy(LeadSource $leadSource)
    {
        $leadSource->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Lead source deleted successfully');
        }

        return redirect()->route('admin.lead-sources.index')->with('success', 'Lead source deleted successfully.');
    }

    public function toggleStatus(LeadSource $leadSource)
    {
        $leadSource->status = $leadSource->status === 'active' ? 'inactive' : 'active';
        $leadSource->save();

        if (request()->ajax()) {
            return $this->jsonSuccess('Status updated successfully', $leadSource);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}

