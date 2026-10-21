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
                    <h4>Patient Registration</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Patient Register</li>
                    </ol>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Patient Register</h4>
                        <a href="{{ url('patients/create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Add New Patient
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="patientTable" class="table table-bordered  text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Ip Number</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Contact</th>
                                        <th>Staff</th>
                                        <th>Date of Admission</th>
                                        <th>Discharge</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patients as $p)
                                    <tr >
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->ip_number }}</td>
                                        <td>{{ $p->name }}</td>
                                        <td>{{ $p->age }}</td>
                                        <td>{{ $p->gender }}</td>
                                        <td>{{ $p->contact_number }}</td>
                                        
                                         <td>{{ $p->staff_name }}</td>
                                        <td>{{ $p->date_of_admission }}</td>
                                      <td>
                                            @if($p->discharge_status === 'Discharged')
                                                <a href="{{ url('patient/discharge_summary/'.$p->id) }}" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="fa-solid fa-eye"></i> View Details
                                                </a>
                                            @else
                                                <a href="{{ url('patients/discharge/'.$p->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fa-solid fa-bed"></i> Make Discharge
                                                </a>
                                            @endif
                                        </td>

                                        <td class="d-flex">
                                            <a href="{{ url('patients/'.$p->id) }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                                            <a href="{{ url('patients/'.$p->id.'/edit') }}" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                                            <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $p->id }}"><i class="fas fa-trash"></i></button>
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

<!-- jQuery & SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables CSS & JS -->
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
    $('#patientTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']], // Sort by Reg No
        columnDefs: [
            { orderable: false, targets: [6] } // Disable sorting for Actions
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

<script>
$(document).ready(function() {
    $('.deleteBtn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('patients') }}/" + id,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire("Deleted!", res.success, "success");
                        // Remove the deleted row from the table
                        $('#patientTable').DataTable().row($('[data-id="'+id+'"]').parents('tr')).remove().draw();
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Something went wrong';
                        Swal.fire("Error", msg, "error");
                    }
                });
            }
        });
    });
});
</script>

@endsection
