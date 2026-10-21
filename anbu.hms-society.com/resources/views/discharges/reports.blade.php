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

            <!-- Filter Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Patient ID</label>
                                <input type="text" id="patient_id" name="patient_id" class="form-control" placeholder="Patient ID" value="{{ request('patient_id') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Patient Name</label>
                                <input type="text" id="patient_name" name="patient_name" class="form-control" placeholder="Patient Name" value="{{ request('patient_name') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Contact No</label>
                                <input type="text" id="contact_no" name="contact_no" class="form-control" placeholder="Contact No" value="{{ request('contact_no') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary w-100" id="resetFilter">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Discharge Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dischargeTable">
                            <thead>
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
                            <tbody id="dischargeTableBody">
                                @include('discharges.partials.discharge_table', ['discharges' => $discharges])
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable with export buttons
    var table = $('#dischargeTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        pageLength: 10,
        order: [[0, 'desc']]
    });

    // Fetch discharges based on filters
    function fetchDischarges() {
        $.ajax({
            url: "{{ url('discharges/reports') }}",
            type: "GET",
            data: {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                patient_id: $('#patient_id').val(),
                patient_name: $('#patient_name').val(),
                contact_no: $('#contact_no').val()
            },
            success: function(res){
                table.destroy(); // destroy old table
                $('#dischargeTableBody').html(res.html);
                table = $('#dischargeTable').DataTable({
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B>>rtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    pageLength: 10,
                    order: [[0, 'desc']]
                });
            }
        });
    }

    // Trigger filter on input change
    $('#from_date, #to_date, #patient_id, #patient_name, #contact_no').on('change keyup', fetchDischarges);

    // Reset filter
    $('#resetFilter').click(function(){
        $('#filterForm')[0].reset();
        fetchDischarges();
    });

    // Delete discharge
    $(document).on('click', '.delete-discharge', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This discharge record will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "{{ url('discharges/delete') }}/" + id,
                    type: "DELETE",
                    data: {_token:'{{ csrf_token() }}'},
                    success: function(res){
                        Swal.fire('Deleted!', res.message, 'success');
                        fetchDischarges();
                    },
                    error: function(){ Swal.fire('Error!','Failed to delete record','error'); }
                });
            }
        });
    });

    // Initial load if URL has query parameters
    @if(request()->hasAny(['from_date','to_date','patient_id','patient_name','contact_no']))
        fetchDischarges();
    @endif

});
</script>
@endsection
