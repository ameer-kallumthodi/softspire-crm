@if($leads->count() > 0)
    @foreach($leads as $index => $lead)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>
            <strong>{{ $lead->name }}</strong><br>
            <small class="text-muted">{{ $lead->country_code }} {{ $lead->phone }}</small>
            @if($lead->email)
            <br><small class="text-muted">{{ $lead->email }}</small>
            @endif
        </td>
        <td>
            <span class="badge bg-primary">{{ $lead->leadStatus ? $lead->leadStatus->name : 'N/A' }}</span>
        </td>
        <td>
            {{ $lead->purpose ? $lead->purpose->name : 'N/A' }}
        </td>
        <td>
            <small>{{ Str::limit($lead->remarks, 50) ?: 'N/A' }}</small>
        </td>
        <td>
            <small>{{ $lead->date ? $lead->date->format('d M, Y') : 'N/A' }}</small>
        </td>
        <td>
            <input type="checkbox" name="lead_id[]" value="{{ $lead->id }}" class="bulk-checkbox lead-checkbox">
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" class="text-center text-muted">No leads found with the selected criteria</td>
    </tr>
@endif
