@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Header -->
                <div class="d-flex justify-content-between mb-3">
                    <h4>Staff Attendance Report</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Attendance Report</li>
                    </ol>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3 mb-3">
                            <div class="col-md-4">
                                <select name="staff_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Select Staff --</option>
                                    @foreach($staff as $st)
                                        <option value="{{ $st->id }}" {{ request('staff_id')==$st->id ? 'selected' : '' }}>
                                            {{ $st->staff_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ url('attendance/patient-report') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                         <h4 class="mb-0">Staff Attendance Day Calculation  </h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendanceTable" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Half Day</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($totals as $name => $counts)
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td>{{ $counts['Present'] }}</td>
                                        <td>{{ $counts['Absent'] }}</td>
                                        <td>{{ $counts['Half Day'] }}</td>
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
</div>

<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'asc']],
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-primary' },
            { extend: 'csv', className: 'btn btn-sm btn-success' },
            { extend: 'excel', className: 'btn btn-sm btn-info' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger' },
            { extend: 'print', className: 'btn btn-sm btn-warning' }
        ],
        responsive: true
    });
});
</script>
@endsection
