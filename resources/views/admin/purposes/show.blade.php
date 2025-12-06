@extends('layouts.admin')

@section('title', 'View Purpose')
@section('page-title', 'View Purpose')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.purposes.index') }}">Purposes</a></li>
<li class="breadcrumb-item active" aria-current="page">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Purpose Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $purpose->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $purpose->name }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $purpose->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($purpose->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $purpose->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $purpose->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.purposes.edit', $purpose) }}" class="btn btn-warning">Edit</a>
                    <a href="{{ route('admin.purposes.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

