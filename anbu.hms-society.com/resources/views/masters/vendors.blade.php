@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 pb-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0">Vendor Master</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Masters</a></li>
                        <li class="breadcrumb-item active">Vendor Masters</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Vendor List</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVendorModal">Add Vendor</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="vendorTable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Vendor Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $index => $vendor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $vendor->vendor_name }}</td>
                                    <td>{{ $vendor->contact_person }}</td>
                                    <td>{{ $vendor->phone }}</td>
                                    <td>{{ $vendor->email }}</td>
                                    <td>{{ $vendor->address }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success editVendorBtn" data-id="{{ $vendor->id }}">
                                            <i class="fa-solid fa-pen-to-square text-white"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger deleteVendorBtn" data-url="{{ url('vendors_delete/'.$vendor->id) }}">
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

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addVendorForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addVendorLabel">Add Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Vendor Name</label>
                        <input type="text" name="vendor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Person Name</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Vendor</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vendor Modal -->
<div class="modal fade" id="editVendorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editVendorForm">
                @csrf
                <input type="hidden" id="edit_vendor_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Vendor Name</label>
                        <input type="text" id="edit_vendor_name" name="vendor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Person Name</label>
                        <input type="text" id="edit_contact_person" name="contact_person" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" id="edit_vendor_phone" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" id="edit_vendor_email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea id="edit_vendor_address" name="address" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Vendor</button>
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

    // Initialize DataTable with buttons
    $('#vendorTable').DataTable({
        dom: '<"d-flex justify-content-between mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy','csv','excel','pdf','print'],
        pageLength: 10
    });

    // Add Vendor
    $("#addVendorForm").submit(function(e){
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Adding...');
        
        $.ajax({
            url: "{{ route('addvendors') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res){
                Swal.fire("Added!", "Vendor added successfully.", "success")
                    .then(()=> location.reload());
            },
            error: function(xhr){
                let msg = "Something went wrong!";
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                } else {
                    msg = xhr.responseText;
                }
                Swal.fire("Error!", msg, "error");
                $btn.prop('disabled', false).html('Add Vendor');
            }
        });
    });

    // Edit Vendor (fetch data)
    $(document).on('click', '.editVendorBtn', function(){
        var id = $(this).data('id');
        $.get("{{ url('vendors_edit') }}/" + id, function(data){
            $("#edit_vendor_id").val(data.id);
            $("#edit_vendor_name").val(data.vendor_name);
            $("#edit_contact_person").val(data.contact_person);
            $("#edit_vendor_phone").val(data.phone);
            $("#edit_vendor_email").val(data.email);
            $("#edit_vendor_address").val(data.address);
            new bootstrap.Modal(document.getElementById('editVendorModal')).show();
        });
    });

    // Update Vendor
    $("#editVendorForm").submit(function(e){
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...');
        
        var id = $("#edit_vendor_id").val();
        $.ajax({
            url: "{{ url('vendors_update') }}/" + id,
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                vendor_name: $("#edit_vendor_name").val(),
                contact_person: $("#edit_contact_person").val(),
                phone: $("#edit_vendor_phone").val(),
                email: $("#edit_vendor_email").val(),
                address: $("#edit_vendor_address").val()
            },
            success: function(res){
                Swal.fire("Updated!", "Vendor updated successfully.", "success")
                    .then(()=> location.reload());
            },
            error: function(xhr){
                let msg = "Something went wrong!";
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                } else {
                    msg = xhr.responseText;
                }
                Swal.fire("Error!", msg, "error");
                $btn.prop('disabled', false).html('Update Vendor');
            }
        });
    });

    // Delete Vendor
    $(document).on('click', '.deleteVendorBtn', function(){
        var url = $(this).data('url');
        Swal.fire({
            title: "Are you sure?",
            text: "This vendor will be deleted!",
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
