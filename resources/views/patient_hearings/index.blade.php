@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <!-- Header & Breadcrumb -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0 text-gradient"> Patient Hearing Reports</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Hearing</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>

            <!-- Hearing Table -->
            <div class="card shadow border-0 rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">Patient Hearing List</h4>
                    <a href="{{ route('hearings.create') }}" class="btn btn-primary btn-sm px-3">+ Add</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('msg'))
                        <div class="alert alert-danger">{{ session('msg') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" id="hearingTable">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>IP Number</th>
                                    <th>Date of Admission</th>
                                    <th>FIR / CSR</th>
                                    <th>Date of Hearing</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hearings as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->patient_name }}</td>
                                    <td>{{ $item->ip_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->date_of_admission)->format('d-m-Y') }}</td>
                                    <td>{{ $item->fir_csr }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->date_of_hearing)->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('hearings.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form id="delete-form-{{ $item->id }}" action="{{ url('hearings/delete', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger deleteHearingBtn" data-id="{{ $item->id }}">
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

<!-- DataTables + Export Buttons -->
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

    // DataTable with export buttons
    $('#hearingTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[0, 'desc']],
        responsive: true
    });

    // SweetAlert Delete Confirmation
    $(document).on('click', '.deleteHearingBtn', function(e){
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: "Confirm Deletion",
            text: "This hearing record will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            confirmButtonColor: "#1cc88a",
            cancelButtonColor: "#d33"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });

});
</script>

<style>
.text-gradient {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    color: transparent;
}
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    transition: 0.3s;
}
.btn-gradient:hover {
    transform: scale(1.03);
    box-shadow: 0 0 12px rgba(76, 175, 80, 0.3);
}
</style>

@endsection
