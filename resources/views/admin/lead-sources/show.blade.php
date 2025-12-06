@extends('layouts.admin')

@section('title', 'View Lead Source')
@section('page-title', 'View Lead Source')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.lead-sources.index') }}">Lead Sources</a></li>
<li class="breadcrumb-item active" aria-current="page">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lead Source Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $leadSource->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $leadSource->name }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $leadSource->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($leadSource->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $leadSource->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $leadSource->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.lead-sources.edit', $leadSource) }}" class="btn btn-warning">Edit</a>
                    <a href="{{ route('admin.lead-sources.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

