@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Discharge Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Discharge Report</li>
                </ol>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Discharge Report</h4>
                </div>

                <div class="card-body">
                    <!-- Discharge Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered " id="dischargeTable" style="width:100%">
                            <thead class="">
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Admission Date</th>
                                    <th>Discharge Date</th>
                                    <th>Duration</th>                                   
                                    <th>Payment Status</th>
                                    <th>Bill & Summary</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($discharges as $index => $discharge)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $discharge->patient_code }}</td>
                                        <td>{{ $discharge->full_name }}</td>
                                        <td>{{ $discharge->admission_datetime }}</td>
                                        <td>{{ $discharge->dishcharge_datetime }}</td>
                                        <td>{{ $discharge->duration }} Months</td>
                                        
                                        <td>{{ $discharge->payment_mode }}</td>
                                       
                                        <td>
                                            <a href="{{ url('discharges/summary/'.$discharge->id) }}" target="_blank" class="btn btn-info btn-sm mb-2">📝 Print Summary</a>
                                            <a href="{{ url('discharges/bill/'.$discharge->id) }}" target="_blank" class="btn btn-primary btn-sm">💰 Print Bill</a>
                                        </td>
                                        <td class="">
                                            <a href="{{ url('discharges/edit/'.$discharge->id) }}" class="btn btn-sm btn-warning mb-1">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-discharge mb-1" data-id="{{ $discharge->id }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
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

<!-- jQuery + DataTables + SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){
    // Init DataTable
    $('#dischargeTable').DataTable({
        responsive: true,
        pageLength: 10,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary btn-sm' },
            { extend: 'csv', className: 'btn btn-info btn-sm' },
            { extend: 'excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm' },
            { extend: 'print', className: 'btn btn-dark btn-sm' }
        ]
    });

    // Delete discharge
    $(document).on('click', '.delete-discharge', function(){
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This discharge record will be cancelled!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "{{ url('discharges/delete') }}/" + id,
                    type: 'DELETE',
                    data: {_token:'{{ csrf_token() }}'},
                    success: function(response){
                        Swal.fire('Deleted!', response.message, 'success').then(() => location.reload());
                    },
                    error: function(){ Swal.fire('Error!', 'Failed to delete record', 'error'); }
                });
            }
        });
    });
});
</script>
@endsection
