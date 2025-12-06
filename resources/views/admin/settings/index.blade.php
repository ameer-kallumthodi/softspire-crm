@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Application Settings</h4>
                <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    @method('PUT')
                    
                    @foreach($settings as $group => $groupSettings)
                    <div class="mb-4">
                        <h5 class="mb-3">{{ ucfirst($group) }}</h5>
                        <div class="row">
                            @foreach($groupSettings as $setting)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                @if($setting->type == 'textarea')
                                <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                @else
                                <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if($settings->isEmpty())
                    <div class="alert alert-info">
                        No settings found. Settings will be created automatically when you save.
                    </div>
                    @endif

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#settingsForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Settings saved successfully', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Something went wrong';
            showToast(errorMessage, 'error');
        }
    });
});
</script>
@endpush

