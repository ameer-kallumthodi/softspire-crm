@extends('layouts.admin')

@section('title', 'Create Lead')
@section('page-title', 'Create Lead')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Create Lead</h4>
                <form action="{{ route('admin.leads.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Country Code <span class="text-danger">*</span></label>
                                <input type="text" name="country_code" class="form-control @error('country_code') is-invalid @enderror" value="{{ old('country_code') }}" required>
                                @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Country <span class="text-danger">*</span></label>
                                <select name="country_id" class="form-control @error('country_id') is-invalid @enderror" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Purpose <span class="text-danger">*</span></label>
                                <select name="purpose_id" class="form-control @error('purpose_id') is-invalid @enderror" required>
                                    <option value="">Select Purpose</option>
                                    @foreach($purposes as $purpose)
                                    <option value="{{ $purpose->id }}" {{ old('purpose_id') == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                                    @endforeach
                                </select>
                                @error('purpose_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Lead Status <span class="text-danger">*</span></label>
                                <select name="lead_status_id" class="form-control @error('lead_status_id') is-invalid @enderror" required>
                                    <option value="">Select Status</option>
                                    @foreach($leadStatuses as $status)
                                    <option value="{{ $status->id }}" {{ old('lead_status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                @error('lead_status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Lead Source <span class="text-danger">*</span></label>
                                <select name="lead_source_id" class="form-control @error('lead_source_id') is-invalid @enderror" required>
                                    <option value="">Select Source</option>
                                    @foreach($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                    @endforeach
                                </select>
                                @error('lead_source_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Telecaller</label>
                                <select name="telecaller_id" class="form-control @error('telecaller_id') is-invalid @enderror">
                                    <option value="">Unassigned</option>
                                    @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ old('telecaller_id') == $telecaller->id ? 'selected' : '' }}>{{ $telecaller->name }}</option>
                                    @endforeach
                                </select>
                                @error('telecaller_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Followup Date</label>
                                <input type="date" name="followup_date" class="form-control @error('followup_date') is-invalid @enderror" value="{{ old('followup_date') }}">
                                @error('followup_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Is Meta</label>
                                <input type="checkbox" name="is_meta" value="1" {{ old('is_meta') ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Is Converted</label>
                                <input type="checkbox" name="is_converted" value="1" {{ old('is_converted') ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks') }}</textarea>
                        @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Lead</button>
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

