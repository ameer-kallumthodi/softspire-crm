@extends('layouts.admin')

@section('title', 'View Lead Status')
@section('page-title', 'View Lead Status')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.lead-statuses.index') }}">Lead Statuses</a></li>
<li class="breadcrumb-item active" aria-current="page">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lead Status Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $leadStatus->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $leadStatus->name }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $leadStatus->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($leadStatus->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $leadStatus->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $leadStatus->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.lead-statuses.edit', $leadStatus) }}" class="btn btn-warning">Edit</a>
                    <a href="{{ route('admin.lead-statuses.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

