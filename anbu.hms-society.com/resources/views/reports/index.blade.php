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
                    <h4>Patient Doc Reports</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Patient Doc Reports</li>
                    </ol>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Doc Reports List</h4>
                        <a href="{{ route('reports.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Add Reports
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportsTable" class="table table-bordered table-striped text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>Document Name</th>
                                        <th>File</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $index => $r)
                                    <tr id="row-{{ $r->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $r->patient_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($r->report_date)->format('d-m-Y') }}</td>
                                        <td>{{ $r->document_name }}</td>
                                        <td>
                                            <a href="{{ asset('uploads/reports/'.$r->document_file) }}" target="_blank" class="btn btn-sm btn-success me-1">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                            <a href="{{ asset('uploads/reports/'.$r->document_file) }}" download class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-download"></i> Download
                                            </a>
                                        </td>
                                        <td class="d-flex justify-content-center gap-2">
                                            <form action="{{ route('reports.destroy', $r->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this report?')">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </form>
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables & Extensions -->
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
    $('#reportsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [5] } // Disable sorting for Actions
        ],
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-danger' },
            { extend: 'csv', className: 'btn btn-sm btn-danger' },
            { extend: 'excel', className: 'btn btn-sm btn-danger' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger' },
            { extend: 'print', className: 'btn btn-sm btn-danger' }
        ],
        responsive: true
    });
});
</script>
@endsection
