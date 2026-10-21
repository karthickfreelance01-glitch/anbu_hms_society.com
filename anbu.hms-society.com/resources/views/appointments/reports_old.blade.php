@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex justify-content-between mb-3">
                <h4>Appointment Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Appointments</li>
                </ol>
            </div>

            <!-- Filter Form -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" id="from_date" name="from_date" value="{{ $data['from_date'] }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" id="to_date" name="to_date" value="{{ $data['to_date'] }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>Patient ID</label>
                                <input type="text" id="search_patient_id" name="search_patient_id" value="{{ $data['search_patient_id'] }}" class="form-control" placeholder="Patient ID">
                            </div>
                            <div class="col-md-3">
                                <label>Patient Name</label>
                                <input type="text" id="search_name" name="search_name" value="{{ $data['search_name'] }}" class="form-control" placeholder="Patient Name">
                            </div>
                            <div class="col-md-2">
                                <label>Contact No</label>
                                <input type="text" id="search_contact" name="search_contact" value="{{ $data['search_contact'] }}" class="form-control" placeholder="Contact No">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary" onclick="resetFilter()" style="height: 32px;margin-top: 20px;">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Appointment Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Appointment List</h4>
                    <a href="{{ url('appointment/create') }}" class="btn btn-primary btn-sm">New Appointment</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="appointmentTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Token No</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Contact Number</th>
                                    <th>Disease Type</th>
                                    <th>Disease Title</th>
                                    <th>Doctor</th>
                                    <th>Appointment Date & Time</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['appo'] as $appointment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $appointment->token_no }}</td>
                                        <td>{{ $appointment->patient_code }}</td>
                                        <td>{{ $appointment->full_name }}</td>
                                        <td>{{ $appointment->contact_number }}</td>
                                        <td>{{ $appointment->symptoms_type }}</td>
                                        <td>{{ $appointment->symptoms_title }}</td>
                                        <td>{{ $appointment->staff_name }}</td>
                                        <td>{{ $appointment->appointment_datetime }}</td>
                                        <td>{{ $appointment->message ?? '-' }}</td>
                                        <td>
                                            @if($appointment->status == 0)
                                                <span class="badge bg-warning">Cancel</span>
                                            @else
                                                <span class="badge bg-success">Success</span>
                                            @endif
                                        </td>
                                        <td class="">
                                            <a href="{{ url('appointment/edit', $appointment->id) }}" class="btn btn-sm btn-white">
                                                <i class="fa-solid fa-pen-to-square text-success"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="cancelAppointmentBtn btn btn-white text-danger" data-url="{{ url('appointments/cancel/'.$appointment->id) }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable
    var table = $('#appointmentTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "order": [[8, 'desc']], // appointment datetime column
    });

    // Cancel Appointment
    function bindCancelButtons(){
        $('.cancelAppointmentBtn').off('click').on('click', function(e){
            e.preventDefault();
            let url = $(this).data('url');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to cancel this appointment!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {_token:"{{ csrf_token() }}"},
                        success:function(){
                            Swal.fire('Cancelled!','Appointment status changed.','success');
                            location.reload(); 
                        },
                        error:function(){ Swal.fire('Error!','Something went wrong!','error'); }
                    });
                }
            });
        });
    }
    bindCancelButtons();

    // Reset filter
    window.resetFilter = function(){
        $('#filterForm')[0].reset();
        table.draw();
    }

    // DataTable custom filtering
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex){
            let from = $('#from_date').val();
            let to = $('#to_date').val();
            let patientId = $('#search_patient_id').val().toLowerCase();
            let name = $('#search_name').val().toLowerCase();
            let contact = $('#search_contact').val().toLowerCase();

            let appDate = new Date(data[8]); // appointment datetime
            let fromDate = from ? new Date(from + 'T00:00:00') : null;
            let toDate = to ? new Date(to + 'T23:59:59') : null;

            if(fromDate && appDate < fromDate) return false;
            if(toDate && appDate > toDate) return false;
            if(patientId && !data[2].toLowerCase().includes(patientId)) return false;
            if(name && !data[3].toLowerCase().includes(name)) return false;
            if(contact && !data[4].toLowerCase().includes(contact)) return false;

            return true;
        }
    );

    // Trigger filter on input change
    $('#from_date, #to_date, #search_patient_id, #search_name, #search_contact').on('keyup change', function(){
        table.draw();
    });

});
</script>
@endsection
