<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\LeadActivity;

class LeadObserver
{
    public function created(Lead $lead)
    {
        // Check if activity already exists to prevent duplicates
        $existingActivity = LeadActivity::where('lead_id', $lead->id)
            ->where('activity_type', 'create')
            ->whereDate('created_at', now()->toDateString())
            ->first();
        
        if (!$existingActivity) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'create',
                'date' => $lead->date,
                'description' => 'Lead created',
            ]);
        }
    }

    public function updated(Lead $lead)
    {
        $changes = $lead->getChanges();
        $original = $lead->getOriginal();

        if (isset($changes['lead_status_id'])) {
            $oldStatusId = $original['lead_status_id'] ?? null;
            $newStatusId = $changes['lead_status_id'];
            
            $oldStatus = $oldStatusId ? \App\Models\LeadStatus::find($oldStatusId)->name ?? 'N/A' : 'N/A';
            $newStatus = \App\Models\LeadStatus::find($newStatusId)->name ?? 'N/A';
            
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'status_change',
                'lead_status_id' => $newStatusId,
                'date' => $lead->date ?? now()->toDateString(),
                'description' => "Status changed from {$oldStatus} to {$newStatus}",
            ]);
        }

        if (isset($changes['followup_date'])) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'followup_update',
                'followup_date' => $changes['followup_date'],
                'date' => $lead->date ?? now()->toDateString(),
                'description' => 'Followup date updated',
            ]);
        }

        if (isset($changes['remarks'])) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'remark_update',
                'remark' => $changes['remarks'],
                'date' => $lead->date ?? now()->toDateString(),
                'description' => 'Remarks updated',
            ]);
        }

        if (!isset($changes['lead_status_id']) && !isset($changes['followup_date']) && !isset($changes['remarks'])) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'update',
                'date' => $lead->date ?? now()->toDateString(),
                'description' => 'Lead updated',
            ]);
        }
    }

    public function deleted(Lead $lead)
    {
        LeadActivity::create([
            'lead_id' => $lead->id,
            'activity_type' => 'delete',
            'date' => now()->toDateString(),
            'description' => 'Lead deleted',
        ]);
    }
}

