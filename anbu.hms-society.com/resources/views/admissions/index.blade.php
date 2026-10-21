@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="d-flex justify-content-between mb-3">
                <h4>Available patients list </h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admission Report</li>
                </ol>
            </div>
              <h2>Overall Pending Amount: <strong>{{ number_format($overallPending, 2) }}</strong></h2>
    
            <!-- Admissions Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Admissions List</h4>
                    <a href="{{ url('admissions/create') }}" class="btn btn-primary btn-sm">Create New Admission</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Admission Date </th>
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
                                        <td>{{ $admission->admission_datetime}}</td>
                                        <td>{{ $admission->duration  }} Monts</td>
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

<!-- Bill Modal -->
<div class="modal fade" id="billModal" tabindex="-1" aria-labelledby="billModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="billForm">
        @csrf
        <input type="hidden" name="admission_id" id="bill_admission_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="billModalLabel">View/Update Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="billInputsContainer"></div>
                <button type="button" id="addBillAmount" class="btn btn-sm btn-secondary mt-2">Add Another Bill</button>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Bill</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

    // Delete Admission
    $('.deleteAdmissionBtn').on('click', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(){
                        Swal.fire('Deleted!','Admission has been deleted.','success');
                        location.reload();
                    },
                    error: function(){
                        Swal.fire('Error!','Something went wrong','error');
                    }
                });
            }
        });
    });

    // View Bill Modal
    $(document).on('click', '.viewBillBtn', function(){
        var admissionId = $(this).data('id');
        var amounts = $(this).data('amount');

        // Parse if string
        if(typeof amounts === 'string'){
            amounts = JSON.parse(amounts);
        }

        $('#bill_admission_id').val(admissionId);
        $('#billInputsContainer').empty();

        // Add input fields for each amount
        amounts.forEach(function(amount){
            $('#billInputsContainer').append(`
                <div class="mb-3 bill-input-wrapper">
                    <label>Bill Amount</label>
                    <input type="number" name="bill_amount[]" class="form-control mb-1" value="${amount}" required>
                </div>
            `);
        });

        var billModal = new bootstrap.Modal(document.getElementById('billModal'));
        billModal.show();
    });

    // Add new bill input dynamically
    $('#addBillAmount').click(function(){
        $('#billInputsContainer').append(`
            <div class="mb-3 bill-input-wrapper">
                <label>Bill Amount</label>
                <input type="number" name="bill_amount[]" class="form-control mb-1" required>
            </div>
        `);
    });

   $('#billForm').submit(function(e){
    e.preventDefault();
    var admissionId = $('#bill_admission_id').val();
    var billAmounts = $("input[name='bill_amount[]']").map(function(){ return $(this).val(); }).get();

    $.ajax({
        url: 'admissions/updatebill/' + admissionId,
        type: 'GET',
        data: {
            _token: '{{ csrf_token() }}',
            admission_fees: billAmounts
        },
        success: function(res){
            Swal.fire('Success','Bill updated successfully!','success');
            $('#billModal').modal('hide');
            location.reload();
        },
        error: function(xhr){
            Swal.fire('Error','Something went wrong','error');
        }
    });
});


});
</script>
@endsection
