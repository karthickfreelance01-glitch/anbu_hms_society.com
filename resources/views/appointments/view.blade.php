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
                    <li class="breadcrumb-item active">Appointment Report</li>
                </ol>
            </div>

            <!-- Appointment Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Appointment List</h4>
                    <a href="{{ url('appointment/create') }}" class="btn btn-primary btn-sm">New Appointment</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="appointmentTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Token No</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Disease Title</th>
                                    <th>Doctor Name</th>
                                    <th>Appointment Date & Time</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                               
                            </thead>
                            <tbody>
                                @forelse($data['appo'] as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $appointment->token_no }}</td>
                                    <td>{{ $appointment->patient_id }}</td>
                                    <td>{{ $appointment->full_name }}</td>
                                    <td>{{ $appointment->symptoms_title ?? '-' }}</td>
                                    <td>{{ $appointment->staff_name ?? '-' }}</td>
                                    <td>{{ $appointment->appointment_datetime }}</td>
                                    <td>{{ $appointment->message ?? '-' }}</td>
                                    <td>
                                        @if($appointment->status == '0')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @elseif($appointment->status == '1')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($appointment->status == '2')
                                            <span class="badge bg-success">Finished</span>
                                        @endif
                                        @if($appointment->status == 2 && $appointment->billing_status != 1)
                                            <a href="{{ url('appointment/billing/' . $appointment->id) }}" class="btn btn-sm btn-info">Generate Bill</a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ url('appointment/edit', $appointment->id) }}" class="btn btn-sm btn-white">
                                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                                            </a>
                                            @if($appointment->status != '2')
                                            <a href="javascript:void(0)" class="statusActionBtn btn btn-sm btn-white text-success"
                                               data-url="{{ route('appointments.finish', $appointment->id) }}"
                                               data-message="mark this appointment as finished?"><i class="fa-solid fa-check"></i></a>
                                            @endif
                                            @if($appointment->status != '1')
                                            <a href="javascript:void(0)" class="statusActionBtn btn btn-sm btn-white text-warning"
                                               data-url="{{ route('appointments.pending', $appointment->id) }}"
                                               data-message="set this appointment to pending?"><i class="fa-solid fa-clock"></i></a>
                                            @endif
                                            @if($appointment->status != '0')
                                            <a href="javascript:void(0)" class="statusActionBtn btn btn-sm btn-white text-danger"
                                               data-url="{{ route('appointments.cancel', $appointment->id) }}"
                                               data-message="cancel this appointment?"><i class="fa-solid fa-xmark"></i></a>
                                            @endif
                                        </div>
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
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable
    var table = $('#appointmentTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true
    });

    // Column Search
    table.columns().every(function(index){
        $('input', table.header().querySelectorAll('.filters th')[index]).on('keyup change', () => {
            table.column(index).search(this.value).draw();
        });
    });

    // SweetAlert status buttons
    $('.statusActionBtn').on('click', function(e){
        e.preventDefault();
        let url = $(this).data('url');
        let message = $(this).data('message');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to " + message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then((result)=>{
            if(result.isConfirmed){
                $.post(url, {_token: "{{ csrf_token() }}"}, function(response){
                    Swal.fire('Updated!', response.message, 'success').then(()=>{location.reload();});
                }).fail(()=>{Swal.fire('Error','Something went wrong!','error')});
            }
        });
    });

});
</script>
@endsection
