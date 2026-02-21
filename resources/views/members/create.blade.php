@extends('layouts.app')

@section('title', 'Register Member')
@section('page-title', 'Register ' . ($type === 'existing' ? 'Existing' : 'New') . ' Member')

@section('content')

<div class="page-header mb-3">
    <h1>Register {{ $type === 'existing' ? 'Existing' : 'New' }} Member</h1>
    <p>Complete all required fields marked with <span style="color:#c0392b;">*</span></p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li style="font-size:13px;">{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('members.store', ['type' => $type]) }}" novalidate id="memberForm">
    @csrf
    @include('members.partials.form', ['type' => $type])
</form>

@endsection
