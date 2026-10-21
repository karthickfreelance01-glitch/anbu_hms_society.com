@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Available Patient Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admission Report</li>
                </ol>
            </div>

            <!-- Filter Form -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label for="from_date">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="to_date">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="patient_id">Patient ID</label>
                                <input type="text" id="patient_id" name="patient_id" class="form-control" placeholder="Enter Patient ID">
                            </div>
                            <div class="col-md-3">
                                <label for="patient_name">Patient Name</label>
                                <input type="text" id="patient_name" name="patient_name" class="form-control" placeholder="Enter Patient Name">
                            </div>
                            <div class="col-md-2">
                                <label for="contact_no">Contact No</label>
                                <input type="text" id="contact_no" name="contact_no" class="form-control" placeholder="Enter Contact No">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary" onclick="resetFilter()" style="height: 32px;margin-top: 20px;">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Admission Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                         <table id="admissionTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Contact No</th>
                                    <th>Admission Date & Time</th>
                                    <th>Duration</th>
                                    <th>Ward No</th>
                                    <th>Consultant Doctor</th>
                                    <th>Consultant Nurse</th>
                                    <th>Therapy</th>
                                    <th>Admission Fees Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="admissionTableBody">
                                @foreach($admissions as $key => $admission)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $admission->patient_code }}</td>
                                        <td>{{ $admission->full_name }}</td>
                                        <td>{{ $admission->contact_number }}</td>
                                        <td>{{ date('d-m-Y H:i', strtotime($admission->admission_datetime)) }}</td>
                                        <td>{{ $admission->duration ?? '-' }} Months</td>
                                        <td>{{ $admission->ward_no ?? '-' }}</td>
                                        <td>{{ optional(DB::table('staff')->where('id', $admission->consultant_doctor_id)->first())->staff_name ?? '-' }}</td>
                                        <td>{{ optional(DB::table('staff')->where('id', $admission->consultant_nurse_id)->first())->staff_name ?? '-' }}</td>
                                        <td>{{ $admission->therapy_names }}</td>
                                        <td>
                                           <a href="{{url('admission_details/'.$admission->id)}}" class="btn btn-info">
                                            View Details</a>
                                        </td>
                                        <td>
                                            <a href="{{ url('admissions/edit/'.$admission->id) }}" class="btn btn-sm btn-warning mb-1">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="{{ url('discharges/create/'.$admission->id) }}" class="btn btn-sm btn-primary mb-1">
                                                <i class="fa-solid fa-bed"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger deleteAdmissionBtn mb-1" data-url="{{ url('admissions/delete/'.$admission->id) }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
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

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function(){

    var table = $('#admissionTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true,
        pagingType: 'simple_numbers',
        language: {
            paginate: {
                previous: '«',
                next: '»'
            },
            search: ""
        }
    });

    $('.search-box').html(`
        <label style="font-weight: bold;"> Search:</label>
        <input type="search" class="form-control form-control-sm ms-2" placeholder="Type to search..." aria-controls="admissionTable" style="display: inline-block; width: auto;">
    `);

    $('.search-box input').on('keyup', function () {
        table.search(this.value).draw();
    });

    function fetchAdmissions() {
        var filters = {};
        if($('#from_date').val()) filters.from_date = $('#from_date').val();
        if($('#to_date').val()) filters.to_date = $('#to_date').val();
        if($('#patient_id').val()) filters.patient_id = $('#patient_id').val();
        if($('#patient_name').val()) filters.patient_name = $('#patient_name').val();
        if($('#contact_no').val()) filters.contact_no = $('#contact_no').val();

        $.ajax({
            url: "{{ url('admissions/reports') }}",
            type: "GET",
            data: filters,
            dataType: "json",
            success: function(response){
                table.clear();
                $('#admissionTableBody').html(response.html);
                table.rows.add($('#admissionTableBody tr')).draw();
                bindDeleteButtons();
            }
        });
    }

    $('#from_date, #to_date, #patient_id, #patient_name, #contact_no').on('keyup change', function(){
        fetchAdmissions();
    });

    window.resetFilter = function(){
        $('#from_date, #to_date, #patient_id, #patient_name, #contact_no').val('');
        fetchAdmissions();
    }

    function bindDeleteButtons(){
        $('.deleteAdmissionBtn').off('click').on('click', function(e){
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action is irreversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(){
                            Swal.fire('Deleted!','Admission has been deleted.','success');
                            fetchAdmissions();
                        },
                        error: function(){
                            Swal.fire('Error!','Something went wrong.','error');
                        }
                    });
                }
            });
        });
    }

    bindDeleteButtons();
});
</script>
@endsection
