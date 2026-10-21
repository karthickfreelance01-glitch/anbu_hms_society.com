@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Medicine Master</h3>
                    <small class="text-muted">Manage medicines</small>
                </div>
                <ol class="breadcrumb m-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Medicine Master</li>
                </ol>
            </div>

            <!-- Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark">Medicine List</h5>
                    <button class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        <i class="fa-solid fa-plus me-1"></i> Add Medicine
                    </button>
                </div>

                <div class="card-body">
                    <table class="table table-striped align-middle" id="medicineTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Medicine Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $index => $med)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $med->medicine_name }}</td>
                                <td>{{ $med->brand }}</td>
                                <td>{{ $med->category }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success rounded-circle editMedicineBtn me-1" data-id="{{ $med->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger rounded-circle deleteMedicineBtn" data-url="{{ url('medicine/delete/'.$med->id) }}">
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

<!-- Add Medicine Modal -->
<div class="modal fade" id="addMedicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addMedicineForm">@csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Medicine Name</label>
                        <input type="text" name="medicine_name" class="form-control" placeholder="Enter Medicine Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Brand</label>
                        <input type="text" name="brand" class="form-control" placeholder="Enter Brand">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="Enter Category">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">Add</button>
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Medicine Modal -->
<div class="modal fade" id="editMedicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editMedicineForm">@csrf
                <input type="hidden" id="edit_medicine_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Edit Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Medicine Name</label>
                        <input type="text" id="edit_medicine_name" name="medicine_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Brand</label>
                        <input type="text" id="edit_brand" name="brand" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Category</label>
                        <input type="text" id="edit_category" name="category" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary px-4">Update</button>
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(function(){
    // Initialize DataTable with export buttons
$('#medicineTable').DataTable({
    pageLength: 10,
    lengthChange: false, // hides the "Show X entries" dropdown
    dom: '<"d-flex justify-content-between mb-2"fB>rtip', // f=search, B=buttons
    buttons: [
        { extend: 'csv', className: 'btn btn-sm btn-primary me-1' },
        { extend: 'excel', className: 'btn btn-sm btn-success me-1' },
        { extend: 'pdf', className: 'btn btn-sm btn-danger me-1' },
        { extend: 'print', className: 'btn btn-sm btn-secondary' }
    ]
});


    // Add medicine
    $('#addMedicineForm').submit(function(e){
        e.preventDefault();
        $.post("{{ url('medicine/add') }}", $(this).serialize(), function(){
            Swal.fire("Added!", "Medicine added successfully!", "success").then(()=>location.reload());
        }).fail(()=>Swal.fire("Error!", "Failed to add medicine.", "error"));
    });

    // Edit medicine
    $(document).on('click','.editMedicineBtn',function(){
        var id=$(this).data('id');
        $.get("{{ url('medicine/edit') }}/"+id,function(data){
            $('#edit_medicine_id').val(data.id);
            $('#edit_medicine_name').val(data.medicine_name);
            $('#edit_brand').val(data.brand);
            $('#edit_category').val(data.category);
            new bootstrap.Modal(document.getElementById('editMedicineModal')).show();
        }).fail(()=>Swal.fire("Error!","Failed to fetch medicine details.","error"));
    });

    // Update medicine
    $('#editMedicineForm').submit(function(e){
        e.preventDefault();
        var id=$('#edit_medicine_id').val();
        $.post("{{ url('medicine/update') }}/"+id, $(this).serialize(), function(){
            Swal.fire("Updated!","Medicine updated successfully!","success").then(()=>location.reload());
        }).fail(()=>Swal.fire("Error!","Update failed.","error"));
    });

    // Delete medicine
    $(document).on('click','.deleteMedicineBtn',function(){
        var url=$(this).data('url');
        Swal.fire({title:'Are you sure?',icon:'warning',showCancelButton:true}).then(res=>{
            if(res.isConfirmed) window.location.href=url;
        });
    });
});
</script>
@endsection
