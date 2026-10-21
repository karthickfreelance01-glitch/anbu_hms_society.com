@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Death Register</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Death Register</li>
                </ol>
            </div>

            <!-- Death Register Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Death Register</h4>
                    <a href="{{ url('death/create') }}" class="btn btn-primary btn-sm">Create New</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="deathRegisterTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Reg No</th>
                                    <th>Patient Name</th>
                                    <th>Death Date & Time</th>
                                    <th>Report</th>
                                    <th>Note</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deaths as $death)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $death->ip_number }}</td>
                                    <td>{{ $death->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($death->death_datetime)->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        @if($death->report_attachment)
                                            <a href="{{ asset('uploads/death_reports/'.$death->report_attachment) }}" target="_blank">View</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $death->note ?? '-' }}</td>
                                    <td>
                                        <a href="{{ url('death/edit/'.$death->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger deleteDeathBtn" data-id="{{ $death->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <form id="delete-form-{{ $death->id }}" action="{{ route('death.destroy', $death->id) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($deaths->isEmpty())
                            <p class="text-center mt-3">No records found.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JS Libraries -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable
    var table = $('#deathRegisterTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true,
        order: [[0, 'desc']]
    });

    // Delete button with SweetAlert
    $(document).on('click', '.deleteDeathBtn', function(){
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $('#delete-form-' + id).submit();
            }
        });
    });

});
</script>
@endsection
