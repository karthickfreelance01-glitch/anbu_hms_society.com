@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Dangerously Illness Records</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Dangerously Ill</a></li>
                        <li class="breadcrumb-item active">Dangerously Ill View</li>
                    </ol>
                </div>
            </div>

            <!-- Records Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Dangerously Ill List</h4>
                        <a href="{{ url('dangerously-ill/create') }}" class="btn btn-primary">Add Record</a>
                    </div>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('msg'))
                        <div class="alert alert-danger">{{ session('msg') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dangerouslyIllTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Admission Date</th>
                                    <th>Before Admission Condition</th>
                                    <th>Treatment Given</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        <td>{{ $record->id }}</td>
                                        <td>{{ $record->patient_name ?? '-' }}</td>
                                        <td>{{ $record->age ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($record->admission_date)->format('d-m-Y') }}</td>
                                        <td>{{ Str::limit($record->before_admission_condition, 40) }}</td>
                                        <td>{{ Str::limit($record->treatment_given, 40) }}</td>
                                        <td>
                                            <a href="{{ url('dangerously-ill/edit/'.$record->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <form id="delete-form-{{ $record->id }}" action="{{ url('dangerously-ill/delete/'.$record->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-danger deleteRecordBtn" data-id="{{ $record->id }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables + Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function(){
    // Initialize DataTable with export buttons
    var table = $('#dangerouslyIllTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[0, 'desc']],
        responsive: true
    });

    // SweetAlert delete confirmation
    $(document).on('click', '.deleteRecordBtn', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        Swal.fire({
            title: "Confirm Deletion",
            text: "This dangerously ill record will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endsection
