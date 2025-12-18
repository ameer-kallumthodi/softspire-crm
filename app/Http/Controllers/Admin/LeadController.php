<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\LeadRequest;
use App\Http\Requests\LeadStatusUpdateRequest;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Country;
use App\Models\Purpose;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Customer;
use App\Helpers\CountriesHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        
        $countries = Country::get(['status' => 'active'], null, null, null, null);
        $leadStatuses = LeadStatus::get(['status' => 'active'], null, null, null, null);
        $leadSources = LeadSource::get(['status' => 'active'], null, null, null, null);

        return view('admin.leads.index', compact('countries', 'leadStatuses', 'leadSources'));
    }

    private function getData(Request $request)
    {
        $where = [];
        $whereOR = [];

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $whereOR['name'] = ['like', "%{$search}%"];
            $whereOR['phone'] = ['like', "%{$search}%"];
            $whereOR['email'] = ['like', "%{$search}%"];
            $where['OR'] = $whereOR;
        }

        if ($request->has('country_id') && $request->country_id != '') {
            $where['country_id'] = $request->country_id;
        }

        if ($request->has('lead_status_id') && $request->lead_status_id != '') {
            $where['lead_status_id'] = $request->lead_status_id;
        }

        if ($request->has('lead_source_id') && $request->lead_source_id != '') {
            $where['lead_source_id'] = $request->lead_source_id;
        }

        // Only show non-converted leads
        $where['is_converted'] = 0;

        $leads = Lead::buildQuery($where, null, ['id', 'desc'], null)
            ->with(['country', 'purpose', 'leadStatus', 'leadSource', 'telecaller'])
            ->get();

        return $this->jsonSuccess('Leads retrieved', $leads);
    }

    public function create()
    {
        $countries = Country::get(['status' => 'active'], null, null, null, null);
        $purposes = Purpose::get(['status' => 'active'], null, null, null, null);
        $leadStatuses = LeadStatus::get(['status' => 'active'], null, null, null, null);
        $leadSources = LeadSource::get(['status' => 'active'], null, null, null, null);
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->get() : collect([]);

        return view('admin.leads.create', compact('countries', 'purposes', 'leadStatuses', 'leadSources', 'telecallers'));
    }

    public function ajaxAdd()
    {
        $countries = Country::get(['status' => 'active'], null, null, null, null);
        $purposes = Purpose::get(['status' => 'active'], null, null, null, null);
        $leadStatuses = LeadStatus::get(['status' => 'active'], null, null, null, null);
        $leadSources = LeadSource::get(['status' => 'active'], null, null, null, null);
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->get() : collect([]);
        $countryCodes = CountriesHelper::getCountryCode();

        return view('admin.leads.ajax_add', compact('countries', 'purposes', 'leadStatuses', 'leadSources', 'telecallers', 'countryCodes'));
    }

    public function ajaxEdit(Lead $lead)
    {
        if ($lead->is_converted) {
            if (request()->ajax()) {
                return $this->jsonError('Cannot edit a converted lead');
            }
            return redirect()->route('admin.leads.show', $lead)->with('error', 'Cannot edit a converted lead');
        }

        $countries = Country::get(['status' => 'active'], null, null, null, null);
        $purposes = Purpose::get(['status' => 'active'], null, null, null, null);
        $leadStatuses = LeadStatus::get(['status' => 'active'], null, null, null, null);
        $leadSources = LeadSource::get(['status' => 'active'], null, null, null, null);
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->get() : collect([]);
        $countryCodes = CountriesHelper::getCountryCode();

        return view('admin.leads.ajax_edit', compact('lead', 'countries', 'purposes', 'leadStatuses', 'leadSources', 'telecallers', 'countryCodes'));
    }

    public function ajaxConvert(Lead $lead)
    {
        $countryCodes = CountriesHelper::getCountryCode();
        return view('admin.leads.ajax_convert', compact('lead', 'countryCodes'));
    }

    public function store(LeadRequest $request)
    {
        $data = $request->validated();
        $data['first_created_at'] = now();
        $data['is_meta'] = $request->has('is_meta') ? 1 : 0;
        $data['is_converted'] = $request->has('is_converted') ? 1 : 0;
        
        // Explicitly ensure lead_status_id and remarks are included (even if empty)
        $data['lead_status_id'] = $request->input('lead_status_id');
        $data['remarks'] = $request->input('remarks', null);
        
        $lead = Lead::create($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead created successfully', $lead);
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['country', 'purpose', 'leadStatus', 'leadSource', 'telecaller', 'activities.leadStatus']);
        
        if (request()->ajax()) {
            return $this->jsonSuccess('Lead retrieved', $lead);
        }
        
        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        if ($lead->is_converted) {
            return redirect()->route('admin.leads.show', $lead)->with('error', 'Cannot edit a converted lead');
        }

        $countries = Country::get(['status' => 'active'], null, null, null, null);
        $purposes = Purpose::get(['status' => 'active'], null, null, null, null);
        $leadStatuses = LeadStatus::get(['status' => 'active'], null, null, null, null);
        $leadSources = LeadSource::get(['status' => 'active'], null, null, null, null);
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->get() : collect([]);

        return view('admin.leads.edit', compact('lead', 'countries', 'purposes', 'leadStatuses', 'leadSources', 'telecallers'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        if ($lead->is_converted) {
            if ($request->ajax()) {
                return $this->jsonError('Cannot update a converted lead');
            }
            return redirect()->route('admin.leads.show', $lead)->with('error', 'Cannot update a converted lead');
        }

        $data = $request->validated();
        $data['is_meta'] = $request->has('is_meta') ? 1 : 0;
        $data['is_converted'] = $request->has('is_converted') ? 1 : 0;
        
        // Explicitly ensure lead_status_id and remarks are included (even if empty)
        $data['lead_status_id'] = $request->input('lead_status_id');
        $data['remarks'] = $request->input('remarks', null);
        
        $lead->update($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead updated successfully', $lead);
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Lead deleted successfully');
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(LeadStatusUpdateRequest $request, Lead $lead)
    {
        if ($lead->is_converted) {
            if ($request->ajax()) {
                return $this->jsonError('Cannot update status of a converted lead');
            }
            return redirect()->route('admin.leads.show', $lead)->with('error', 'Cannot update status of a converted lead');
        }

        $oldStatusId = $lead->lead_status_id;
        $oldStatus = LeadStatus::find($oldStatusId);
        $oldStatusName = $oldStatus->name ?? 'N/A';
        
        $lead->lead_status_id = $request->lead_status_id;
        $lead->remarks = $request->remarks;
        $lead->date = $request->date;
        
        // Handle followup date
        if (($request->input('needed_followup') == '1' || $request->input('needed_followup') === true) && $request->has('followup_date')) {
            $lead->followup_date = $request->followup_date;
        } else {
            $lead->followup_date = null;
        }
        
        $lead->saveQuietly();

        $newStatus = LeadStatus::find($request->lead_status_id);
        $newStatusName = $newStatus->name ?? 'N/A';

        $activityData = [
            'lead_id' => $lead->id,
            'activity_type' => 'status_change',
            'lead_status_id' => $request->lead_status_id,
            'remark' => $request->remarks,
            'date' => $request->date,
            'description' => "Status changed from {$oldStatusName} to {$newStatusName}",
        ];

        // Add followup_date to activity if provided
        if (($request->input('needed_followup') == '1' || $request->input('needed_followup') === true) && $request->has('followup_date')) {
            $activityData['followup_date'] = $request->followup_date;
        }

        LeadActivity::create($activityData);

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead status updated successfully', $lead->fresh());
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead status updated successfully.');
    }

    public function convert(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Update lead with new data
        $lead->name = $request->name;
        $lead->country_code = $request->country_code;
        $lead->phone = $request->phone;
        $lead->email = $request->email;
        $lead->is_converted = 1;
        $lead->save();

        // Create customer record
        $customer = Customer::create([
            'lead_id' => $lead->id,
            'name' => $request->name,
            'country_code' => $request->country_code,
            'phone' => $request->phone,
            'country_id' => $lead->country_id,
            'purpose_id' => $lead->purpose_id,
            'telecaller_id' => $lead->telecaller_id,
            'email' => $request->email,
            'converted_date' => now(),
            'converted_by' => Auth::id(),
        ]);

        // Create lead activity
        LeadActivity::create([
            'lead_id' => $lead->id,
            'activity_type' => 'lead_converted',
            'date' => now(),
            'description' => 'Lead converted to customer',
        ]);

        if ($request->ajax()) {
            return $this->jsonSuccess('Lead converted successfully', $customer);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Lead converted successfully.');
    }
}

