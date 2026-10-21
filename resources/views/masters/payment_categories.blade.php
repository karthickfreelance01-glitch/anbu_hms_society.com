@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Account Type Master</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Masters</a></li>
                        <li class="breadcrumb-item active">Account Type</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Account Type List</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Account Type</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="categoryTable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Account Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $cat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $cat->payment_category }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success editCategoryBtn" data-id="{{ $cat->id }}">
                                            <i class="fa-solid fa-pen-to-square text-white"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger deleteCategoryBtn" data-url="{{ url('payment-categories/delete/'.$cat->id) }}">
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addCategoryForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Account Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Account Type </label>
                        <input type="text" name="payment_category" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Category</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm">
                @csrf
                <input type="hidden" id="edit_category_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Account Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Account Type</label>
                        <input type="text" id="edit_category_name" name="payment_category" class="form-control" required>
                    </div>                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
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
    $('#categoryTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy','csv','excel','pdf','print'],
        pageLength: 10
    });

    // Reusable function for form submission with loading button
    function handleFormSubmit(form, url, data, successMsg){
        let submitBtn = $(form).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.data('originalText', submitBtn.html());
        submitBtn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Please wait...');

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(res){
                Swal.fire("Success!", successMsg, "success").then(()=> location.reload());
            },
            error: function(xhr){
                const msg = xhr.responseJSON?.message || xhr.responseText || "Something went wrong!";
                Swal.fire("Error!", msg, "error");
            },
            complete: function(){
                submitBtn.prop('disabled', false).html(submitBtn.data('originalText'));
            }
        });
    }

    // Add Payment Category
    $("#addCategoryForm").submit(function(e){
        e.preventDefault();
        handleFormSubmit(this, "{{ route('payment_categories.store') }}", $(this).serialize(), "Account Type added successfully.");
    });

    // Edit Category (fetch data)
    $(document).on('click', '.editCategoryBtn', function(){
        var id = $(this).data('id');
        $.get("{{ url('payment-categories/edit') }}/" + id, function(data){
            $("#edit_category_id").val(data.id);
            $("#edit_category_name").val(data.payment_category);
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        });
    });

    // Update Payment Category
    $("#editCategoryForm").submit(function(e){
        e.preventDefault();
        var id = $("#edit_category_id").val();
        handleFormSubmit(this, "{{ url('payment-categories/update') }}/" + id, {
            _token: '{{ csrf_token() }}',
            payment_category: $("#edit_category_name").val()
        }, "Account Type updated successfully.");
    });

    // Delete Category
    $(document).on('click', '.deleteCategoryBtn', function(){
        var url = $(this).data('url');
        Swal.fire({
            title: "Are you sure?",
            text: "This category will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = url;
            }
        });
    });

});
</script>

@endsection
