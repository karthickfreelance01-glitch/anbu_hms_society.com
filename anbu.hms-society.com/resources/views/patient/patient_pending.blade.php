@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <h3 class="fw-bold text-primary mb-4">Patient-wise Pending Report</h3>

                <!-- Filter Form -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('patient.pending.reports') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Patient</label>
                                <select name="patient_id" class="form-select select2" data-placeholder="Select Patient">
                                    <option value="">-- All Patients --</option>
                                    @foreach($patients as $id => $name)
                                        <option value="{{ $id }}" {{ ($patient_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ optional($from_date)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ optional($to_date)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('patient.pending.reports') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h6>Medicine Pending</h6>
                                <h4 class="text-danger">₹ {{ number_format($overallMedicinePending, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h6>Patient Pending</h6>
                                <h4 class="text-danger">₹ {{ number_format($overallPatientPending, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h6>Overall Pending</h6>
                                <h4 class="text-danger">₹ {{ number_format($overallPending, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Table -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-info text-white"><h6>Patient Pending Summary</h6></div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Medicine Pending</th>
                                    <th>Patient Pending</th>
                                    <th>Overall Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report as $key => $row)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $row['patient_name'] }}</td>
                                    <td>₹ {{ number_format($row['medicine_pending'], 2) }}</td>
                                    <td>₹ {{ number_format($row['patient_pending'], 2) }}</td>
                                    <td>₹ {{ number_format($row['overall_pending'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('.select2').select2({ width:'100%', allowClear:true, placeholder:function(){ return $(this).data('placeholder'); } });
});
</script>
@endsection
