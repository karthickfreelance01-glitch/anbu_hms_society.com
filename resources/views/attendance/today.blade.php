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

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Today's Attendance - {{ \Carbon\Carbon::parse($today)->format('d-m-Y') }}</h4>\
                        <a href="{{ url('attendance') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Mark / Update Attendance
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendanceTable" class="table table-bordered table-striped text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Roles</th>
                                        <th class="text-center">Staff Name</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendance as $att)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $att->role }}</td>
                                        <td>{{ $att->staff_name }}</td>
                                        <td>
                                            @if($att->status == 'Present')
                                                <span class="badge bg-success">Present</span>
                                            @elseif($att->status == 'Absent')
                                                <span class="badge bg-danger">Absent</span>
                                            @elseif($att->status == 'Leave')
                                                <span class="badge bg-warning text-dark">Leave</span>
                                            @elseif($att->status == 'Half Day')
                                                <span class="badge bg-primary">Half Day</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
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
