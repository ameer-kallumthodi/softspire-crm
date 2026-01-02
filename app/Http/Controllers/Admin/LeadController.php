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
use App\Models\Department;
use App\Helpers\CountriesHelper;
use App\Helpers\PhoneNumberHelper;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        // If user is Digital Marketing employee, only show leads assigned to them
        $user = Auth::user();
        if ($user && $user->role && $user->role->slug === 'employee' && $user->department && $user->department->name === 'Digital Marketing') {
            $where['user_type'] = 'digital_marketing';
            $where['user_id'] = $user->id;
        }

        $leads = Lead::buildQuery($where, null, ['id', 'desc'], null)
            ->with(['country', 'purpose', 'leadStatus', 'leadSource', 'telecaller', 'user'])
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
        
        // Get Digital Marketing employees
        $digitalMarketingDept = Department::where('name', 'Digital Marketing')->first();
        $digitalMarketingEmployees = $digitalMarketingDept ? User::where('department_id', $digitalMarketingDept->id)
            ->where('status', 'active')
            ->get() : collect([]);
        
        $countryCodes = CountriesHelper::getCountryCode();

        return view('admin.leads.ajax_add', compact('countries', 'purposes', 'leadStatuses', 'leadSources', 'telecallers', 'digitalMarketingEmployees', 'countryCodes'));
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
        
        // Get Digital Marketing employees
        $digitalMarketingDept = Department::where('name', 'Digital Marketing')->first();
        $digitalMarketingEmployees = $digitalMarketingDept ? User::where('department_id', $digitalMarketingDept->id)
            ->where('status', 'active')
            ->get() : collect([]);
        
        $countryCodes = CountriesHelper::getCountryCode();

        return view('admin.leads.ajax_edit', compact('lead', 'countries', 'purposes', 'leadStatuses', 'leadSources', 'telecallers', 'digitalMarketingEmployees', 'countryCodes'));
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
        
        // Handle user_type and user_id
        if ($request->has('user_type')) {
            $data['user_type'] = $request->input('user_type');
            if ($request->input('user_type') === 'telecaller') {
                $data['user_id'] = $request->input('telecaller_id');
                $data['telecaller_id'] = $request->input('telecaller_id');
            } elseif ($request->input('user_type') === 'digital_marketing') {
                $data['user_id'] = $request->input('user_id');
                $data['telecaller_id'] = null;
            }
        } else {
            // Default to telecaller for backward compatibility
            $data['user_type'] = 'telecaller';
            $data['user_id'] = $request->input('telecaller_id');
        }
        
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
        
        // Handle user_type and user_id
        if ($request->has('user_type')) {
            $data['user_type'] = $request->input('user_type');
            if ($request->input('user_type') === 'telecaller') {
                $data['user_id'] = $request->input('telecaller_id');
                $data['telecaller_id'] = $request->input('telecaller_id');
            } elseif ($request->input('user_type') === 'digital_marketing') {
                $data['user_id'] = $request->input('user_id');
                $data['telecaller_id'] = null;
            }
        } else {
            // Default to telecaller for backward compatibility
            $data['user_type'] = 'telecaller';
            $data['user_id'] = $request->input('telecaller_id');
        }
        
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

    public function bulkUploadView()
    {
        $leadStatuses = LeadStatus::get(['status' => 'active'], null, null, null, null);
        $leadSources = LeadSource::get(['status' => 'active'], null, null, null, null);
        $purposes = Purpose::get(['status' => 'active'], null, null, null, null);
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->get() : collect([]);

        return view('admin.leads.ajax_bulk_upload', compact('leadStatuses', 'leadSources', 'purposes', 'telecallers'));
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/lead-sample.xlsx');
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }
        
        $currentDateTime = now()->format('Y-m-d_H-i-s');
        $filename = "Lead_Bulk_Upload_Template_{$currentDateTime}.xlsx";
        
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function bulkUploadSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'purpose_id' => 'required|exists:purposes,id',
            'assign_to_all' => 'nullable|boolean',
            'telecallers' => 'required_if:assign_to_all,false|array|min:1',
            'telecallers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return $this->jsonError('Validation failed', 422, $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('excel_file');
            
            if (!$file || !$file->isValid()) {
                $errorMessage = 'File upload failed. Maximum file size: 2MB.';
                if ($request->ajax()) {
                    return $this->jsonError($errorMessage);
                }
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            if ($highestRow < 2) {
                $errorMessage = 'Excel file appears to be empty or has no data rows.';
                if ($request->ajax()) {
                    return $this->jsonError($errorMessage);
                }
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            // Get telecallers based on assignment type
            if ($request->assign_to_all) {
                $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
                $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->pluck('id')->toArray() : [];
                
                if (empty($telecallers)) {
                    $errorMessage = 'No telecallers found. Please assign telecallers manually.';
                    if ($request->ajax()) {
                        return $this->jsonError($errorMessage);
                    }
                    return redirect()->back()->with('error', $errorMessage)->withInput();
                }
            } else {
                $telecallers = $request->telecallers ?? [];
                
                if (empty($telecallers)) {
                    $errorMessage = 'Please select at least one telecaller or choose "Assign to all telecallers".';
                    if ($request->ajax()) {
                        return $this->jsonError($errorMessage);
                    }
                    return redirect()->back()->with('error', $errorMessage)->withInput();
                }
            }

            $telecallerIndex = 0;
            $successCount = 0;
            $duplicateCount = 0;
            $errorCount = 0;
            $errors = [];

            // Limit rows to prevent timeout
            $maxRows = min($highestRow, 1000);
            
            for ($row = 2; $row <= $maxRows; $row++) {
                $name = trim($worksheet->getCell('A' . $row)->getValue());
                $phone = trim($worksheet->getCell('B' . $row)->getValue());
                $place = trim($worksheet->getCell('C' . $row)->getValue());
                $remarks = trim($worksheet->getCell('D' . $row)->getValue());

                // Skip empty rows
                if (empty($name) && empty($phone)) {
                    continue;
                }

                // Validate required fields
                if (empty($name) || empty($phone)) {
                    $errorCount++;
                    $errors[] = "Row {$row}: Name and Phone are required";
                    continue;
                }

                // Parse phone number
                $phoneData = PhoneNumberHelper::get_phone_code($phone);
                $countryCode = $phoneData['code'];
                $phoneNumber = $phoneData['phone'];
                
                if (empty($countryCode) || empty($phoneNumber)) {
                    $errorCount++;
                    $errors[] = "Row {$row}: Invalid phone number format";
                    continue;
                }

                // Find country by matching name from CountriesHelper
                $countryName = CountriesHelper::getName($countryCode);
                $country = Country::where('name', 'LIKE', '%' . $countryName . '%')
                    ->where('status', 'active')
                    ->first();
                
                // If not found, use first active country as default
                if (!$country) {
                    $country = Country::get(['status' => 'active'], null, null, 1, null)->first();
                }
                
                if (!$country) {
                    $errorCount++;
                    $errors[] = "Row {$row}: No active country found. Please add countries first.";
                    continue;
                }

                // Check if lead already exists
                $existingLead = Lead::where('country_code', $countryCode)
                    ->where('phone', $phoneNumber)
                    ->whereNull('deleted_at')
                    ->first();
                    
                if ($existingLead) {
                    $duplicateCount++;
                    continue;
                }

                // Get telecaller
                $telecallerId = $telecallers[$telecallerIndex] ?? $telecallers[0];
                
                // Combine place and remarks
                $combinedRemarks = trim($place . ($remarks ? ' - ' . $remarks : ''));
                
                try {
                    // Create lead without triggering observer events to prevent duplicate activities
                    $lead = new Lead([
                        'name' => $name,
                        'country_code' => $countryCode,
                        'phone' => $phoneNumber,
                        'country_id' => $country->id,
                        'purpose_id' => $request->purpose_id,
                        'lead_status_id' => $request->lead_status_id,
                        'lead_source_id' => $request->lead_source_id,
                        'telecaller_id' => $telecallerId,
                        'remarks' => $combinedRemarks ?: null,
                        'date' => now(),
                        'first_created_at' => now(),
                        'is_converted' => 0,
                        'is_meta' => 0,
                    ]);
                    
                    // Save without events to prevent observer from firing
                    $lead->saveQuietly();

                    if ($lead) {
                        $successCount++;
                        
                        // Log activity manually (observer is disabled via saveQuietly)
                        // Check if activity already exists to prevent duplicates
                        $existingActivity = LeadActivity::where('lead_id', $lead->id)
                            ->where('activity_type', 'bulk_upload')
                            ->whereDate('created_at', now()->toDateString())
                            ->first();
                        
                        if (!$existingActivity) {
                            LeadActivity::create([
                                'lead_id' => $lead->id,
                                'activity_type' => 'bulk_upload',
                                'description' => 'Lead created via bulk upload',
                                'date' => now()->toDateString(),
                            ]);
                        }
                        
                        $telecallerIndex = ($telecallerIndex + 1) % count($telecallers);
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row {$row}: " . $e->getMessage();
                }
            }

            $message = "Successfully uploaded {$successCount} leads!";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicates skipped.";
            }
            if ($errorCount > 0) {
                $message .= " {$errorCount} errors occurred.";
            }

            if ($request->ajax()) {
                return $this->jsonSuccess($message, [
                    'success_count' => $successCount,
                    'duplicate_count' => $duplicateCount,
                    'error_count' => $errorCount,
                    'errors' => $errors
                ]);
            }

            return redirect()->route('admin.leads.index')->with('success', $message);
        } catch (\Exception $e) {
            $errorMessage = 'Error processing file: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return $this->jsonError($errorMessage);
            }
            
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    public function getTelecallersByTeam(Request $request)
    {
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        
        if (!$telecallerRole) {
            return response()->json(['telecallers' => []]);
        }
        
        $telecallers = User::where('role_id', $telecallerRole->id)
            ->select('id', 'name')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name
                ];
            });
        
        return response()->json(['telecallers' => $telecallers]);
    }

    /**
     * Show bulk reassign form
     */
    public function ajaxBulkReassign()
    {
        $currentUser = AuthHelper::user();
        $telecallerRole = UserRole::get(['slug' => 'telecaller'], null, null, 1, null)->first();
        
        // Get all telecallers
        $telecallers = $telecallerRole ? User::where('role_id', $telecallerRole->id)->get() : collect([]);
        
        $data = [
            'telecallers' => $telecallers,
            'leadStatuses' => LeadStatus::get(['status' => 'active'], null, null, null, null),
            'leadSources' => LeadSource::get(['status' => 'active'], null, null, null, null),
            'purposes' => Purpose::get(['status' => 'active'], null, null, null, null),
        ];

        return view('admin.leads.ajax-bulk-reassign', $data);
    }

    /**
     * Process bulk reassign
     */
    public function bulkReassign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'from_telecaller_id' => 'required|exists:users,id',
            'lead_from_date' => 'required|date',
            'lead_to_date' => 'required|date',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return $this->jsonError('Validation failed', 422, $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get telecaller names for activity history
        $toTelecaller = User::find($request->telecaller_id);
        $fromTelecaller = User::find($request->from_telecaller_id);
        
        $toTelecallerName = $toTelecaller ? $toTelecaller->name : 'Unknown';
        $fromTelecallerName = $fromTelecaller ? $fromTelecaller->name : 'Unknown';

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            // Update the lead
            $updated = Lead::where('id', $leadId)
                ->whereNull('deleted_at')
                ->update([
                    'telecaller_id' => $request->telecaller_id,
                    'lead_source_id' => $request->lead_source_id,
                    'lead_status_id' => $request->lead_status_id,
                ]);

            if ($updated) {
                // Create lead activity history
                LeadActivity::create([
                    'lead_id' => $leadId,
                    'lead_status_id' => $request->lead_status_id,
                    'activity_type' => 'bulk_reassign',
                    'description' => 'Lead reassigned via bulk operation',
                    'remark' => "Lead has been reassigned from telecaller {$fromTelecallerName} to telecaller {$toTelecallerName}.",
                    'date' => now()->toDateString(),
                ]);

                $successCount++;
            }
        }

        if ($request->ajax()) {
            return $this->jsonSuccess("Successfully reassigned {$successCount} leads!");
        }

        return redirect()->back()->with('success', "Successfully reassigned {$successCount} leads!");
    }

    /**
     * Get leads by source for reassign operations
     */
    public function getLeadsBySourceReassign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_source_id' => 'required|exists:lead_sources,id',
            'tele_caller_id' => 'required|exists:users,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'purpose_id' => 'nullable|exists:purposes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid parameters'], 422);
        }

        $fromDate = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $toDate = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));
        
        $query = Lead::select([
            'id', 'name', 'country_code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'purpose_id', 'telecaller_id', 'remarks', 'date', 'is_converted', 'created_at'
        ])
        ->where('lead_source_id', $request->lead_source_id)
        ->where('telecaller_id', $request->tele_caller_id)
        ->where('lead_status_id', $request->lead_status_id)
        ->where('is_converted', 0)
        ->whereNull('deleted_at')
        ->whereBetween('created_at', [$fromDate, $toDate]);
        
        // Optional purpose filter
        if ($request->filled('purpose_id')) {
            $query->where('purpose_id', $request->purpose_id);
        }
        
        $leads = $query->with([
            'leadStatus:id,name', 
            'leadSource:id,name', 
            'telecaller:id,name', 
            'purpose:id,name'
        ])
        ->orderBy('created_at', 'desc')
        ->get();
        
        return view('admin.leads.partials.leads-table-rows-reassign', compact('leads'));
    }
}

