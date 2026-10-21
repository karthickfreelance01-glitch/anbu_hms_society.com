@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
<div class="container my-5">
    <h3 class="text-center text-success mb-4">Select Patient for Discharge Bill</h3>

    <form action="" method="get">
        <div class="mb-3">
            <label for="patient_id" class="form-label">Patient Name</label>
            <select class="form-select" id="patient_id" onchange="if(this.value) window.location.href='/patient/discharge/'+this.value;">
                <option value="">-- Select Patient --</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }} (IP: {{ $patient->ip_number }})</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@endsection
