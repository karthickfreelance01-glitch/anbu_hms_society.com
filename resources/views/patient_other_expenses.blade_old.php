@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Patient Other Expenses</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Patient Other Expenses</li>
                    </ol>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="mb-0">Patient Expenses</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        Add Expense
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="expenseTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reg No</th>
                                <th>Patient Name</th>
                                <th>Amount (₹)</th>
                                <th>Date</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['patient_expenses'] as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->reg_no }}</td>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->amount }}</td>
                                <td>{{ $p->date }}</td>
                                <td>{{ $p->notes }}</td>
                                <td>
                                    @if($p->status == 'Pending')
                                        <button 
                                            class="btn btn-sm btn-warning toggleStatusBtn" 
                                            data-id="{{ $p->id }}" 
                                            data-current="Pending"
                                        >
                                            💰 Payment Pending — <strong>Mark as Collected?</strong>
                                        </button>
                                    @else
                                        <button 
                                            class="btn btn-sm btn-success toggleStatusBtn" 
                                            data-id="{{ $p->id }}" 
                                            data-current="Collected"
                                        >
                                            ✅ Payment Collected — <strong>Mark as Pending?</strong>
                                        </button>
                                    @endif
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-success editExpenseBtn" data-id="{{ $p->id }}">
                                        <i class="fa-solid fa-pen-to-square text-white"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger deleteExpenseBtn" data-id="{{ $p->id }}">
                                        <i class="fa-solid fa-trash text-white"></i>
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

<!-- ✅ Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addExpenseForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->reg_no }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editExpenseForm">
                @csrf
                <input type="hidden" id="edit_expense_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Patient</label>
                        <select id="edit_patient_id" name="patient_id" class="form-control" required>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->reg_no }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" step="0.01" id="edit_amount" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" id="edit_date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea id="edit_notes" name="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(function() {
    $('#expenseTable').DataTable();

    // ✅ Add Expense
    $('#addExpenseForm').on('submit', function(e){
        e.preventDefault();
        $.post("{{ url('patient-expense/store') }}", $(this).serialize(), function(res){
            Swal.fire('Success', res.message, 'success').then(()=>location.reload());
        }).fail(function(xhr){
            Swal.fire('Error', xhr.responseJSON.message, 'error');
        });
    });

    // ✅ Edit Expense
    $(document).on('click', '.editExpenseBtn', function(){
        var id = $(this).data('id');
        $.get("{{ url('patient-expense/edit') }}/" + id, function(data){
            $('#edit_expense_id').val(data.id);
            $('#edit_patient_id').val(data.patient_id);
            $('#edit_amount').val(data.amount);
            $('#edit_date').val(data.date);
            $('#edit_notes').val(data.notes);
            new bootstrap.Modal('#editExpenseModal').show();
        });
    });

    // ✅ Update Expense
    $('#editExpenseForm').on('submit', function(e){
        e.preventDefault();
        var id = $('#edit_expense_id').val();
        $.post("{{ url('patient-expense/update') }}/" + id, $(this).serialize(), function(res){
            Swal.fire('Updated!', res.message, 'success').then(()=>location.reload());
        });
    });

    // ✅ Delete Expense
    $(document).on('click', '.deleteExpenseBtn', function(){
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result)=>{
            if(result.isConfirmed){
                $.get("{{ url('patient-expense/delete') }}/" + id, function(res){
                    Swal.fire('Deleted!', res.message, 'success').then(()=>location.reload());
                });
            }
        });
    });

    // ✅ Toggle Status (with question)
    $(document).on('click', '.toggleStatusBtn', function(){
        let id = $(this).data('id');
        let current = $(this).data('current');
        let question = current === 'Pending' 
            ? 'Has the payment been collected for this patient?' 
            : 'Do you want to mark this payment as pending again?';

        Swal.fire({
            title: 'Change Payment Status',
            text: question,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel'
        }).then((result)=>{
            if(result.isConfirmed){
                $.get("{{ url('patient-expense/change-status') }}/" + id, function(res){
                    Swal.fire('Updated!', res.message, 'success').then(()=>location.reload());
                });
            }
        });
    });
});
</script>
@endsection
