@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Death Reports</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Death Reports</li>
                </ol>
            </div>

            <!-- Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label for="from_date">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="to_date">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="patient_id">Patient ID</label>
                                <input type="text" id="patient_id" name="patient_id" class="form-control" placeholder="Enter Patient ID">
                            </div>
                            <div class="col-md-3">
                                <label for="patient_name">Patient Name</label>
                                <input type="text" id="patient_name" name="patient_name" class="form-control" placeholder="Enter Patient Name">
                            </div>
                            <div class="col-md-2">
                                <label for="contact_no">Contact No</label>
                                <input type="text" id="contact_no" name="contact_no" class="form-control" placeholder="Enter Contact No">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary w-100" onclick="resetFilter()" style="height:32px;">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="deathRegisterTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Contact Number</th>
                                    <th>Death Date & Time</th>
                                    <th>Report</th>
                                    <th>Note</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="deathTableBody">
                                @forelse($deaths as $death)
                                <tr>
                                    <td>{{ $death->id }}</td>
                                    <td>{{ $death->patient_id }}</td>
                                    <td>{{ $death->full_name }}</td>
                                    <td>{{ $death->contact_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($death->death_datetime)->format('d-m-Y H:i') }}</td>
                                    <td>
                                        @if($death->report_attachment)
                                            <a href="{{ asset('uploads/death_reports/'.$death->report_attachment) }}" target="_blank">View</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $death->note ?? '-' }}</td>
                                    <td>
                                        <a href="{{ url('death/edit/'.$death->id) }}">
                                            <i class="fa-solid fa-pen-to-square text-success"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-white deleteDeathBtn" data-id="{{ $death->id }}">
                                            <i class="fa-solid fa-trash text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $death->id }}" action="{{ route('death.destroy', $death->id) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
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

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable with export buttons
    var table = $('#deathRegisterTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']]
    });

    function fetchDeaths() {
        $.ajax({
            url: "{{ route('death.reports') }}",
            type: "GET",
            data: {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                patient_id: $('#patient_id').val(),
                patient_name: $('#patient_name').val(),
                contact_no: $('#contact_no').val()
            },
            success: function(res){
                table.destroy(); // destroy old table
                $('#deathTableBody').html(res.html);
                table = $('#deathRegisterTable').DataTable({
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B>>rtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    responsive: true,
                    pageLength: 10,
                    order: [[0, 'desc']]
                });
                bindDeleteButtons();
            },
            error: function(){
                Swal.fire("Error!", "Failed to fetch records.", "error");
            }
        });
    }

    // Filter change
    $('#from_date, #to_date, #patient_id, #patient_name, #contact_no').on('change keyup', fetchDeaths);

    // Reset filter
    window.resetFilter = function(){
        $('#filterForm')[0].reset();
        fetchDeaths();
    }

    // Delete binding
    function bindDeleteButtons(){
        $('.deleteDeathBtn').off('click').on('click', function(e){
            e.preventDefault();
            var id = $(this).data("id");
            Swal.fire({
                title: "Are you sure?",
                text: "This record will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if(result.isConfirmed){
                    $("#delete-form-" + id).submit();
                }
            });
        });
    }

    bindDeleteButtons();

});
</script>
@endsection
