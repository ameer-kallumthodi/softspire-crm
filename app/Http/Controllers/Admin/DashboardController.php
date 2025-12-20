<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Chart 1: Leads over the last 30 days (daily breakdown)
        $leadsOverTime = [];
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            $count = Lead::buildQuery(null, null, null, null)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();
            $leadsOverTime[] = $count;
        }

        // Chart 2: Leads by Source
        $leadsBySource = Lead::buildQuery(null, null, null, null)
            ->select('lead_source_id', DB::raw('count(*) as count'))
            ->groupBy('lead_source_id')
            ->get()
            ->map(function($item) {
                $source = LeadSource::find($item->lead_source_id);
                return [
                    'name' => $source->name ?? 'Unknown',
                    'count' => $item->count
                ];
            })
            ->sortByDesc('count')
            ->values();

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
            'leadsOverTime' => $leadsOverTime,
            'leadsOverTimeLabels' => $labels,
            'leadsBySource' => $leadsBySource,
        ];

        return view('admin.dashboard', $data);
    }
}

