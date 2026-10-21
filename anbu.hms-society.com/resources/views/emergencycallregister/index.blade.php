@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-flex justify-content-between mb-3">
                    <h4>Emergency Call Register</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Emergency Call Register</li>
                    </ol>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Emergency Call Records</h4>
                        <a href="{{ route('emergency.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> New Record
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="emergencyTable" class="table table-bordered table-striped text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>Illness</th>
                                        <th>Doctor</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($emergencies as $index => $e)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $e->patient_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($e->date)->format('d-m-Y') }}</td>
                                        <td>{{ $e->illness }}</td>
                                        <td>{{ $e->doctor }}</td>
                                        <td>{{ $e->remarks }}</td>
                                        <td>
                                            <a href="{{ route('emergency.edit', $e->id) }}" class="btn btn-sm btn-warning mb-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger mb-1 deleteBtn" data-id="{{ $e->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#emergencyTable').DataTable({ pageLength: 10, order: [[2,'desc']] });

    $('.deleteBtn').click(function() {
        let id = $(this).data('id');
        let token = '{{ csrf_token() }}';
        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: 'emergency_call_register/' + id,
                    type: 'DELETE',
                    data: { _token: token },
                    success: function(res){
                        Swal.fire('Deleted!', res.message, 'success').then(()=> location.reload());
                    },
                    error: function(xhr){
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
