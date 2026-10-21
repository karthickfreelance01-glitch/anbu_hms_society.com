@extends('layout.admin')

@section('content')

   @php
$roles = [
    "Accountant",
    "Administrator",
    "Admin Staff",
    "Ambulance Driver",
    "Cleaner",
    "Coordinator & Counselor",
    "Cook",
    "Counselor & Psychiatrist",
    "Dietician",
    "Doctor",
    "Driver",
    "HR Manager",
    "Lab Technician",
    "Managing Director",
    "Manager",
    "Nurse",
    "Office Clerk",
    "Pharmacist",
    "Physiologist",
    "PSW",
    "Radiologist",
    "Receptionist",
    "Security",
    "Supervisor",
    "Warden",
    "Ward Boy"
];

sort($roles); // A → Z
@endphp

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Staff Master</h3>
                    <small class="text-muted">Manage staff details</small>
                </div>
                <ol class="breadcrumb m-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Staff Master</li>
                </ol>
            </div>

            <!-- Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark">Staff List</h5>
                    <button class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i class="fa-solid fa-plus me-1"></i> Add Staff
                    </button>
                </div>

                <div class="card-body">
                    <table class="table align-middle" id="staffTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Name</th>
                                <th>Role</th>
                                <th>Contact No</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Join Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $index => $st)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $st->staff_name }}</td>
                                <td>{{ $st->role }}</td>
                                <td>{{ $st->contact_no }}</td>
                                <td>{{ $st->email }}</td>
                                <td>{{ $st->address }}</td>
                                <td>{{ \Carbon\Carbon::parse($st->join_date)->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success rounded-circle editStaffBtn me-1" data-id="{{ $st->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger rounded-circle deleteStaffBtn" data-url="{{ url('staff_delete/'.$st->id) }}">
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

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addStaffForm">@csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Staff Name</label>
                        <input type="text" name="staff_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Staff Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Contact No</label>
                        <input type="text" name="contact_no" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Join Date</label>
                        <input type="date" name="join_date" class="form-control">
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

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editStaffForm">@csrf
                <input type="hidden" id="edit_staff_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Edit Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Staff Name</label>
                        <input type="text" id="edit_staff_name" name="staff_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Staff Role</label>
                        <select id="edit_role" name="role" class="form-control" required>
                            @foreach($roles as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Contact No</label>
                        <input type="text" id="edit_contact_no" name="contact_no" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" id="edit_email" name="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Address</label>
                        <textarea id="edit_address" name="address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Join Date</label>
                        <input type="date" id="edit_join_date" name="join_date" class="form-control" required>
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

<!-- JS Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(function(){

    $('#staffTable').DataTable({ pageLength: 10 });

    // Add staff
    $('#addStaffForm').submit(function(e){
        e.preventDefault();
        $.post("{{ url('staff_add') }}", $(this).serialize(), function(){
            Swal.fire("Added!", "Staff added successfully!", "success")
                .then(()=>location.reload());
        }).fail(()=>{
            Swal.fire("Error!", "Failed to add staff.", "error");
        });
    });

    // Load staff data into edit modal
    $(document).on('click','.editStaffBtn',function(){
        var id = $(this).data('id');
        $.get("{{ url('staff_edit') }}/"+id, function(data){

            $('#edit_staff_id').val(data.id);
            $('#edit_staff_name').val(data.staff_name);
            $('#edit_role').val(data.role);
            $('#edit_contact_no').val(data.contact_no);
            $('#edit_email').val(data.email);
            $('#edit_address').val(data.address);
            $('#edit_join_date').val(data.join_date);

            new bootstrap.Modal(document.getElementById('editStaffModal')).show();
        });
    });

    // Update staff
    $('#editStaffForm').submit(function(e){
        e.preventDefault();
        var id = $('#edit_staff_id').val();

        $.post("{{ url('staff_update') }}/"+id, $(this).serialize(), function(){
            Swal.fire("Updated!","Staff updated successfully!","success")
                .then(()=>location.reload());
        });
    });

    // Delete staff
    $(document).on('click','.deleteStaffBtn',function(){
        var url = $(this).data('url');

        Swal.fire({
            title:'Are you sure?',
            icon:'warning',
            showCancelButton:true
        }).then(res=>{
            if(res.isConfirmed) window.location.href = url;
        });
    });

});
</script>

@endsection
