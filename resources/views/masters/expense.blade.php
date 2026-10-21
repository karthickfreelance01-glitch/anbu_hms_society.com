@extends('layout.admin')

@section('content')
<style>

</style>

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Expense Master</h3>
                    
                </div>
                <ol class="breadcrumb m-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Masters</a></li>
                    <li class="breadcrumb-item active">Expense Master</li>
                </ol>
            </div>

            <!-- Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark">Expense List</h5>
                    <button class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="fa-solid fa-plus me-1"></i> Add Expense
                    </button>
                </div>

                <div class="card-body">
                    <table class="table align-middle" id="expenseTable">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Expense Name</th>
                                <th class="text-center" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $index => $exp)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $exp->expense_name }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success rounded-circle editExpenseBtn me-1" data-id="{{ $exp->id }}" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger rounded-circle deleteExpenseBtn" data-url="{{ url('expenses_delete/'.$exp->id) }}" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
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

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addExpenseForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Expense Name</label>
                        <input type="text" name="expense_name" class="form-control" placeholder="Enter Expense Name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4" id="addBtn">Add</button>
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editExpenseForm">
                @csrf
                <input type="hidden" id="edit_expense_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Edit Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Expense Name</label>
                        <input type="text" id="edit_expense_name" name="expense_name" class="form-control" placeholder="Enter Expense Name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary px-4" id="updateBtn">Update</button>
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function(){
    // Initialize DataTable
    $('#expenseTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy','csv','excel','pdf','print'],
        pageLength: 10
    });

    // Add Expense
    $("#addExpenseForm").submit(function(e){
        e.preventDefault();
        var $btn = $("#addBtn").prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Adding...');
        $.post("{{ url('addexpenses') }}", $(this).serialize(), function(){
            Swal.fire("Added!", "Expense added successfully.", "success").then(()=>location.reload());
        }).fail(function(xhr){
            Swal.fire("Error!", xhr.responseText, "error");
            $btn.prop('disabled', false).html('Add');
        });
    });

    // Edit Expense - Fetch details and show modal
    $(document).on('click', '.editExpenseBtn', function(){
        var id = $(this).data('id');
        $.get("{{ url('expenses_edit') }}/" + id, function(data){
            if(data && data.id){
                $('#edit_expense_id').val(data.id);
                $('#edit_expense_name').val(data.expense_name);
                var editModal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
                editModal.show();
            } else {
                Swal.fire("Error!", "Expense data not found!", "error");
            }
        }).fail(function(){
            Swal.fire("Error!", "Unable to fetch expense details.", "error");
        });
    });

    // Update Expense
    $('#editExpenseForm').submit(function(e){
        e.preventDefault();
        var id = $('#edit_expense_id').val();
        var $btn = $("#updateBtn").prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...');

        $.ajax({
            url: "{{ url('expenses_update') }}/" + id,
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                expense_name: $('#edit_expense_name').val()
            },
            success: function(){
                Swal.fire("Updated!", "Expense updated successfully.", "success").then(()=>location.reload());
            },
            error: function(xhr){
                let msg = xhr.responseJSON?.message || xhr.responseText || "Something went wrong.";
                Swal.fire("Error!", msg, "error");
                $btn.prop('disabled', false).html('Update');
            }
        });
    });

    // Delete Expense
    $(document).on('click', '.deleteExpenseBtn', function(){
        var url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This expense will be deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-danger rounded-pill', cancelButton: 'btn btn-secondary rounded-pill' },
            buttonsStyling: false
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = url;
            }
        });
    });
});
</script>
@endsection
