@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="card shadow-lg rounded-4 p-4 mt-4">
                    <h3 class="text-primary fw-bold mb-4 text-center">Medicine Issue Report</h3>

                    <!-- Filter Form -->
                    <form action="{{ route('medicine-issues.report.filter') }}" method="POST" class="row g-3 mb-4">
                        @csrf
                        <div class="col-md-6">
                            <label for="patient_id" class="form-label">Select Patient</label>
                            <select name="patient_id" id="patient_id" class="form-select" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ (isset($selected_patient) && $selected_patient == $p->id) ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-success">Filter Report</button>
                            <a href="{{ route('medicine-issues.report') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    <!-- Report Table -->
                    <div class="table-responsive">
                        <table id="medicineReportTable" class="table table-bordered table-striped">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Reg No</th>
                                    <th>Patient Name</th>
                                    <th>Admission Date</th>
                                    <th>Drug Name</th>
                                    <th>Drug Count</th>
                                    <th>Issue Date</th>
                                    <th>Issue Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($issues as $index => $issue)
                                <tr class="text-center">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $issue->reg_no ?? '-' }}</td>
                                    <td>{{ $issue->patient_name }}</td>
                                    <td>{{ $issue->date_of_admission }}</td>
                                    <td>{{ $issue->drug_name }}</td>
                                    <td>{{ $issue->drug_count }}</td>
                                    <td>{{ $issue->issue_date }}</td>
                                    <td>{{ $issue->issue_time }}</td>
                                </tr>
                                @empty
                               
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Include JS for DataTables and Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    

    // Initialize Select2 for patient dropdown
    $('#patient_id').select2({
        placeholder: '-- Select Patient --',
        allowClear: true
    });
});
</script>

@endsection
