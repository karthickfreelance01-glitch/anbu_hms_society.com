@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- 🔹 Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary">Patient Discharge</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Discharge</li>
                </ol>
            </div>

            <!-- 🔹 Select Patient Card -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-secondary mb-3">Select a Patient to Discharge</h5>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Select Patient (Reg No / Name)</label>
                            <select id="patientSelect" class="form-select select2">
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->ip_number }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 text-end">
                            <button id="openFormBtn" class="btn btn-primary px-4" disabled>
                                <i class="fa-solid fa-arrow-right me-2"></i>Get Discharge Form
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 🔹 JS and CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "-- Select Patient --",
        allowClear: true
    });

    $('#patientSelect').change(function() {
        const id = $(this).val();
        $('#openFormBtn').prop('disabled', !id);
    });

    $('#openFormBtn').click(function() {
        const id = $('#patientSelect').val();
        if (id) {
            window.location.href = "{{ url('patients/discharge') }}/" + id;
        }
    });
});
</script>
@endsection
