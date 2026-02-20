@extends('layouts.app')

@section('title', 'Edit — ' . $member->name_with_initials)
@section('page-title', 'Edit Member')

@section('content')

<div class="page-header mb-3">
    <h1>Edit: {{ $member->name_with_initials }}</h1>
    <p>Update member information. Fields marked with <span style="color:#c0392b;">*</span> are required.</p>
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

<form method="POST" action="{{ route('members.update', $member) }}" novalidate id="memberForm">
    @csrf
    @method('PUT')
    @include('members.partials.form')
</form>

@endsection
