@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h4 class="fw-bold mb-0">Expense MainCategory Master</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Masters</a></li>
                    <li class="breadcrumb-item active">Expense MainCategory Master</li>
                </ol>
            </div>

            <!-- Card -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0">
                    <h5 class="mb-0">Expense MainCategory List</h5>
                    <button class="btn btn-primary btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="fa-solid fa-plus me-1"></i> Add Expense
                    </button>
                </div>
                <div class="card-body ">
                    <table class="table table-bordered table-hover mb-0" id="expenseTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Expense MainCategory</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $index => $exp)
                            <tr class="align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $exp->expensescategory }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success rounded-circle editExpenseBtn me-1" data-id="{{ $exp->id }}" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger rounded-circle deleteExpenseBtn" data-id="{{ $exp->id }}" title="Delete">
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
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-sm">
            <form id="addExpenseForm">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Add Expense MainCategory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="expense_category" class="form-control form-control-lg shadow-sm" placeholder="Enter expense category" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success rounded-pill" id="addBtn">Add</button>
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-sm">
            <form id="editExpenseForm">
                @csrf
                <input type="hidden" id="edit_expense_id">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Edit Expense MainCategory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="edit_expense_name" name="expense_category" class="form-control form-control-lg shadow-sm" placeholder="Enter expense category" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill" id="updateBtn">Update</button>
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    // Add Expense with Loading
    $("#addExpenseForm").submit(function (e) {
        e.preventDefault();
        var $btn = $("#addBtn");
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Adding...');
        $.ajax({
            url: "{{ route('addexpensescatgory') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    Swal.fire("Success!", res.message, "success").then(() => location.reload());
                } else {
                    Swal.fire("Error!", res.message, "error");
                    $btn.prop('disabled', false).html('Add');
                }
            },
            error: function () {
                Swal.fire("Error!", "Something went wrong. Try again.", "error");
                $btn.prop('disabled', false).html('Add');
            }
        });
    });

    // Open Edit Modal
    $(document).on("click", ".editExpenseBtn", function () {
        var id = $(this).data("id");
        $.get("{{ url('expenses-category/edit') }}/" + id, function (data) {
            $("#edit_expense_id").val(data.id);
            $("#edit_expense_name").val(data.expensescategory);
            new bootstrap.Modal(document.getElementById("editExpenseModal")).show();
        });
    });

    // Update Expense with Loading
    $("#editExpenseForm").submit(function (e) {
        e.preventDefault();
        var id = $("#edit_expense_id").val();
        var $btn = $("#updateBtn");
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...');
        $.ajax({
            url: "{{ url('expenses-category/update') }}/" + id,
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    Swal.fire("Updated!", res.message, "success").then(() => location.reload());
                } else {
                    Swal.fire("Error!", res.message, "error");
                    $btn.prop('disabled', false).html('Update');
                }
            },
            error: function () {
                Swal.fire("Error!", "Something went wrong while updating.", "error");
                $btn.prop('disabled', false).html('Update');
            }
        });
    });

    // Delete Expense with SweetAlert
    $(document).on("click", ".deleteExpenseBtn", function () {
        var id = $(this).data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "This expense MainCategory will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill',
                cancelButton: 'btn btn-secondary rounded-pill'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('expenses-category/delete') }}/" + id,
                    method: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire("Deleted!", res.message, "success").then(() => location.reload());
                        } else {
                            Swal.fire("Error!", res.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error!", "Unable to delete record.", "error");
                    }
                });
            }
        });
    });
});
</script>
@endsection
