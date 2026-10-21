@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Escape Register List</h4>
                <a href="{{ route('escape.create') }}" class="btn btn-primary">Add Escape</a>
            </div>

            <!-- Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-2">
                        <div class="col-md-2">
                            <label>From Date</label>
                            <input type="date" id="from_date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>To Date</label>
                            <input type="date" id="to_date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Patient ID</label>
                            <input type="text" id="patient_id" class="form-control" placeholder="Patient ID">
                        </div>
                        <div class="col-md-3">
                            <label>Patient Name</label>
                            <input type="text" id="patient_name" class="form-control" placeholder="Patient Name">
                        </div>
                        <div class="col-md-2">
                            <label>Contact No</label>
                            <input type="text" id="contact_no" class="form-control" placeholder="Contact No">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary w-100" id="resetFilter">Reset</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="escapeRegisterTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Contact No</th>
                                    <th>Escape Date & Time</th>
                                    <th>Video</th>
                                    <th>Police FIR</th>
                                    <th>File Attach</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="escapeTableBody">
                                @include('escape.partials.escape_table', ['escapes' => $escapes])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function(){
    var table = $('#escapeRegisterTable').DataTable({
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B>>rtip', // removed 'f' (search box)
        buttons: ['copy','csv','excel','pdf','print'],
        pageLength: 10,
        order: [[0,'desc']]
    });

    function fetchEscapes() {
        $.get("{{ url('escape/reports') }}", {
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            patient_id: $('#patient_id').val(),
            patient_name: $('#patient_name').val(),
            contact_no: $('#contact_no').val()
        }, function(response){
            table.destroy();
            $('#escapeTableBody').html(response.html);
            table = $('#escapeRegisterTable').DataTable({
                responsive: true,
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B>>rtip', // removed search
                buttons: ['copy','csv','excel','pdf','print'],
                pageLength: 10,
                order: [[0,'desc']]
            });
        });
    }

    $('#from_date,#to_date,#patient_id,#patient_name,#contact_no').on('change keyup', fetchEscapes);
    $('#resetFilter').on('click', function(){
        $('#filterForm')[0].reset();
        fetchEscapes();
    });

    $(document).on('click','.deleteEscapeBtn',function(){
        var id = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "This record will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then((result)=>{
            if(result.isConfirmed) $('#delete-form-'+id).submit();
        });
    });
});
</script>

@endsection
