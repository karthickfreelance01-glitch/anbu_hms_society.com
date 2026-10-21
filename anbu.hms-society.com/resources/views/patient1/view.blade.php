@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="d-flex justify-content-between mb-4 align-items-center">
                <h4 class="mb-0">Patient Profile</h4>
                <a href="{{ url('patients') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
            </div>

            <div class="card shadow-lg rounded-lg overflow-hidden">
                <!-- Header with photo -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 p-4 text-white d-flex align-items-center">
                    @if($patient->photo)
                        <div class="me-4" style="width:120px; height:120px;">
                            <img src="{{ asset('uploads/patients/'.$patient->photo) }}" 
                                 alt="{{ $patient->name }}" 
                                 class="rounded-circle" 
                                 style="width:100%; height:100%;  border:3px solid #fff;">
                        </div>
                    @endif
                    <div>
                        <h3 class="mb-1">{{ $patient->name }}</h3>
                        <small class="d-block">Registration No: <strong>{{ $patient->reg_no }}</strong></small>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark">Age: {{ $patient->age }}</span>
                            <span class="badge bg-light text-dark">{{ ucfirst($patient->gender) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Date of Admission</h6>
                            <p>{{ $patient->date_of_admission }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Time of Admission</h6>
                            <p>{{ $patient->time_of_admission }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Contact Number</h6>
                            <p>{{ $patient->contact_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Present Address</h6>
                            <p>{{ $patient->present_address }}</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col text-center">
                            <a href="{{ url('patients/'.$patient->id.'/edit') }}" class="btn btn-primary me-2">Edit Profile</a>

                            <button type="button" class="btn btn-danger" id="deleteBtn">Delete Profile</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.card-body h6 { font-size: 0.9rem; font-weight: 500; }
.card-body p { font-size: 1rem; margin-bottom: 0.5rem; }
.bg-gradient-to-r { background: linear-gradient(to right, #3b82f6, #6366f1); }
</style>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('deleteBtn').addEventListener('click', function(e){
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form dynamically and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ url('patients/'.$patient->id) }}";

            // CSRF token
            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);

            // DELETE method
            let method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    })
});
</script>
@endsection
