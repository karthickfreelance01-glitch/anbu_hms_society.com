@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Escape Register</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Escape </a></li>
                        <li class="breadcrumb-item active">Escape View</li>
                    </ol>
                </div>
            </div>
            
            <!-- Escape Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Escape  List</h4>
                        <a href="{{ route('escape.create') }}" class="btn btn-primary">Add Escape</a>
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
                        <table class="table table-bordered" id="escapeRegisterTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Escape Date & Time</th>
                                    <th>Video</th>
                                    <th>Police FIR</th>
                                    <th>File Attach</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="escapeTableBody">
                                @forelse($escapes as $escape)
                                    <tr>
                                        <td>{{ $escape->id }}</td>
                                        <td>{{ $escape->full_name ?? $escape->patient_name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($escape->escape_datetime)->format('d-m-Y h:i A') }}</td>
                                        <td>
                                            @if($escape->video_footage)
                                                <a href="{{ asset($escape->video_footage) }}" target="_blank">View Video</a>
                                            @else N/A @endif
                                        </td>
                                        <td>
                                            @if($escape->police_fir_copy)
                                                <a href="{{ asset($escape->police_fir_copy) }}" target="_blank">View FIR</a>
                                            @else N/A @endif
                                        </td>
                                        <td>
                                            @if($escape->file_attach)
                                                <a href="{{ asset($escape->file_attach) }}" target="_blank">View File</a>
                                            @else N/A @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('escape.edit', $escape->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <form id="delete-form-{{ $escape->id }}" action="{{ route('escape.destroy', $escape->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger deleteEscapeBtn" data-id="{{ $escape->id }}">
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

    var table = $('#escapeRegisterTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[0, 'desc']],
        responsive: true
    });

    // AJAX fetch and update tbody
    function fetchEscapes() {
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            url: "{{ url('escape_view') }}",
            type: "GET",
            data: { from_date: from_date, to_date: to_date },
            dataType: "json"
        }).done(function(response){
            if(response.html !== undefined) {
                table.destroy();  // Destroy DataTable before updating tbody
                $('#escapeTableBody').html(response.html);

                table = $('#escapeRegisterTable').DataTable({
                    dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    order: [[0, 'desc']],
                    responsive: true
                });
            } else {
                Swal.fire('Error','Invalid server response','error');
            }
        }).fail(function(){
            Swal.fire('Error','Failed to fetch data','error');
        });
    }

    $('#from_date, #to_date').on('change', fetchEscapes);
    $('#resetFilter').on('click', function(){
        $('#from_date').val('');
        $('#to_date').val('');
        fetchEscapes();
    });

    $(document).on('click', '.deleteEscapeBtn', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        Swal.fire({
            title: "Confirm Deletion",
            text: "This record will be deleted permanently.",
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
