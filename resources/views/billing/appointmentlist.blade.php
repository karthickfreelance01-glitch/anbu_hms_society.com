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
                    <li class="breadcrumb-item active">Appointment Bill</li>
                </ol>
            </div>

            <!-- Appointment Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Appointment Bill</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="appointmentTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Token No</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Generate Bill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['appo'] as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $appointment->token_no }}</td>
                                    <td>{{ $appointment->patient_id }}</td>
                                    <td>{{ $appointment->full_name }}</td>
                                    <td>
                                        @if($appointment->status == 2 && $appointment->billing_status != 2)
                                            <a href="{{ url('appointment/billing/' . $appointment->id) }}" class="btn btn-sm btn-info">
                                                Generate Bill
                                            </a>
                                        @endif
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

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable with export buttons
    $('#appointmentTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: [
            { extend: 'excelHtml5', title: 'Appointment_Bill' },
            { extend: 'csvHtml5', title: 'Appointment_Bill' },
            { extend: 'pdfHtml5', title: 'Appointment_Bill', orientation: 'portrait', pageSize: 'A4' },
            { extend: 'print', title: 'Appointment Bill' }
        ],
        paging: true,
        searching: true,
        info: true,
        order: [[0, 'asc']],   // ✅ comma was missing here
        language: {
            paginate: {
                previous: '«',
                next: '»'
            }
        }
    });

    // SweetAlert example if needed
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
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_token: "{{ csrf_token() }}"},
                    success:function(response){
                        Swal.fire('Updated!', response.message, 'success')
                        .then(() => { location.reload(); });
                    },
                    error:function(){
                        Swal.fire('Error!','Something went wrong!','error');
                    }
                });
            }
        });
    });

});
</script>

@endsection
