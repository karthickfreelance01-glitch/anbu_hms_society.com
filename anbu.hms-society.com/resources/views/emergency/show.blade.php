@extends('layout.admin')

@section('content')
<div class="main-wrapper">
@include('include.header')
@include('include.sidebar')

<div class="page-wrapper">
<div class="content">
<div class="card shadow-lg rounded-4 p-4">
    <h4>Emergency Hospitalization Details</h4>
    <p><strong>Patient:</strong> {{ $record->reg_no }} - {{ $record->name }}</p>
    <p><strong>Date:</strong> {{ $record->date }}</p>
    <p><strong>Illness:</strong> {{ $record->illness }}</p>
    <p><strong>Doctor:</strong> {{ $record->doctor }}</p>
    <p><strong>Remarks:</strong> {{ $record->remarks }}</p>
    <a href="{{ route('emergency.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
</div>
</div>
</div>
@endsection
