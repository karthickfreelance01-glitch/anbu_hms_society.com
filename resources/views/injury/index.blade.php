@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Injury Register</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Injury</a></li>
                        <li class="breadcrumb-item active">Injury View</li>
                    </ol>
                </div>
            </div>

            <!-- Injury Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Injury List</h4>
                        <a href="{{ route('injuries.create') }}" class="btn btn-primary">Add Injury</a>
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
                        <table class="table table-bordered" id="injuryTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Admission Date</th>
                                    <th>Injury Date</th>
                                    <th>Injury Time</th>
                                    <th>Incident</th>
                                    <th>Physician</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="injuryTableBody">
                                @forelse($injuries as $injury)
                                    <tr>
                                        <td>{{ $injury->id }}</td>
                                        <td>{{ $injury->patient_name ?? '-' }}</td>
                                        <td>{{ $injury->age ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($injury->admission_date)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($injury->injury_date)->format('d-m-Y') }}</td>
                                        <td>{{ $injury->injury_time }}</td>
                                        <td>{{ Str::limit($injury->incident, 30) }}</td>
                                        <td>{{ $injury->physician }}</td>
                                        <td>
                                            <a href="{{ route('injuries.edit', $injury->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <form id="delete-form-{{ $injury->id }}" action="{{ route('injuries.destroy', $injury->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger deleteInjuryBtn" data-id="{{ $injury->id }}">
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
    var table = $('#injuryTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[0, 'desc']],
        responsive: true
    });

    // SweetAlert delete confirmation
    $(document).on('click', '.deleteInjuryBtn', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        Swal.fire({
            title: "Confirm Deletion",
            text: "This injury record will be deleted permanently.",
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
