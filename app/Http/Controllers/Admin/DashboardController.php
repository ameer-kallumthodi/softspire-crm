<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display dashboard
     */
    public function index()
    {
        $totalLeads = Lead::get(null, null, null, null, null)->count();
        $newLeads = Lead::get(['created_at' => ['operator', '>=', now()->subDays(30)]], null, null, null, null)->count();
        $totalUsers = User::count();
        
        $activeStatusIds = LeadStatus::get(['status' => 'active'], ['id'], null, null, null)->pluck('id')->toArray();
        $activeLeads = Lead::get(['lead_status_id' => $activeStatusIds], null, null, null, null)->count();
        
        $convertedLeads = Lead::get(['is_converted' => 1], null, null, null, null)->count();
        $todayLeads = Lead::get(['created_at' => ['operator', '>=', today()->startOfDay()]], null, null, null, null)->count();
        $thisWeekLeads = Lead::get(['created_at' => ['operator', '>=', now()->startOfWeek()]], null, null, null, null)->count();
        $thisMonthLeads = Lead::get(['created_at' => ['operator', '>=', now()->startOfMonth()]], null, null, null, null)->count();

        $recentLeads = Lead::buildQuery(null, null, ['created_at', 'desc'], null)
            ->with(['country', 'leadStatus', 'leadSource', 'telecaller'])
            ->limit(10)
            ->get();

        $leadsByStatus = Lead::get(null, ['lead_status_id'], null, null, 'lead_status_id');
        $leadsByStatus = $leadsByStatus->groupBy('lead_status_id')->map(function($items, $statusId) {
            $status = LeadStatus::find($statusId);
            return [
                'name' => $status->name ?? 'Unknown',
                'count' => $items->count()
            ];
        })->values();

        $data = [
            'totalLeads' => $totalLeads,
            'newLeads' => $newLeads,
            'totalUsers' => $totalUsers,
            'activeLeads' => $activeLeads,
            'convertedLeads' => $convertedLeads,
            'todayLeads' => $todayLeads,
            'thisWeekLeads' => $thisWeekLeads,
            'thisMonthLeads' => $thisMonthLeads,
            'recentLeads' => $recentLeads,
            'leadsByStatus' => $leadsByStatus,
        ];

        return view('admin.dashboard', $data);
    }
}

