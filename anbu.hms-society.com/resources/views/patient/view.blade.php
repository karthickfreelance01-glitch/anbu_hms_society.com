@extends('layout.admin')

@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

```
<div class="page-wrapper">
    <div class="content pb-0">
        <div class="container-fluid">

            <!-- GLASS CARD -->
            <div class="card card-glass border-0 p-4 mt-4 animate__animated animate__fadeIn">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-gradient-animate">🏥 Patient Profile View</h2>
                    <p class="text-muted">Patient personal and admission details</p>
                    <hr class="animated-hr mt-2 mb-3 mx-auto w-50">
                </div>

                <!-- PHOTO PREVIEW -->
                <div class="row justify-content-end mb-3">
                    <div class="col-md-4 text-center">
                        @if($patient->photo)
                            <div class="photo-box">
                                <img src="{{ asset('/'.$patient->photo) }}" 
                                     style="width:100%;height:100%;border-radius:16px;object-fit:cover;">
                            </div>
                        @else
                            <div class="photo-box">No Photo</div>
                        @endif
                    </div>
                </div>

                <!-- INFO SECTION -->
                <div class="section-title mb-3">👤 Personal Details</div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold">IP Number</label><div class="form-control">{{$patient->ip_number}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Name</label><div class="form-control">{{$patient->name}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Age</label><div class="form-control">{{$patient->age}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Gender</label><div class="form-control">{{$patient->gender}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Marital Status</label><div class="form-control">{{$patient->marital_status ?? '-'}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Occupation</label><div class="form-control">{{$patient->occupation ?? '-'}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Guardian</label><div class="form-control">{{$patient->admitted_by ?? '-'}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Contact</label><div class="form-control">{{$patient->contact_number}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Referral</label><div class="form-control">{{$patient->referral_source ?? '-'}}</div></div>
                </div>

                <hr class="my-4">

                <div class="section-title mb-3">⏱ Admission Details</div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold">Admission Date</label><div class="form-control">{{$patient->date_of_admission}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Admission Time</label><div class="form-control">{{$patient->time_of_admission}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Patient Case</label><div class="form-control">{{$patient->patient_case ?? '-'}}</div></div>
                </div>

                <hr class="my-4">

                <div class="section-title mb-3">📍 Address</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold">Present Address</label><div class="form-control">{{$patient->present_address}}</div></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Permanent Address</label><div class="form-control">{{$patient->permanent_address ?? '-'}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Blood Group</label><div class="form-control">{{$patient->blood_group ?? '-'}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Staff Name</label><div class="form-control">{{$patient->staff_name ?? '-'}}</div></div>
                </div>

                <hr class="my-4">

                <div class="section-title mb-3">💰 Billing Info</div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold">Monthly Amount (₹)</label><div class="form-control">{{$patient->monthly_amount}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Received (₹)</label><div class="form-control">{{$patient->received_amount}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Pending (₹)</label><div class="form-control">{{$patient->pending_amount}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Payment Mode</label><div class="form-control">{{ucfirst($patient->payment_mode)}}</div></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Account Type</label><div class="form-control">{{$patient->account_type}}</div></div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="text-center mt-4">
                    <a href="{{ url('patients/'.$patient->id.'/edit') }}" class="btn btn-upload-modern me-2">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </a>

                    <button class="btn btn-submit-modern bg-danger text-white" id="deleteBtn">
                        <i class="bi bi-trash"></i> Delete Profile
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>
```

</div>

<!-- SweetAlert -->

<script>
document.getElementById('deleteBtn').addEventListener('click', function(e){
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ url('patients/'.$patient->id) }}";

            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);

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

<style>
.card-glass {
    background: rgba(255,255,255,0.3);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.4);
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}
.photo-box {
    height: 160px;
    border-radius: 16px;
    background: #eaf6ff;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#2575fc;
    font-weight:600;
}
.section-title {
    font-size:15px;
    font-weight:700;
    color:#2575fc;
}
.text-gradient-animate {
    background: linear-gradient(90deg, #6a11cb, #2575fc, #2af598, #009efd);
    background-size: 300% 100%;
    -webkit-background-clip: text;
    color: transparent;
    animation: gradientMove 4s linear infinite;
}
@keyframes gradientMove {
    0%{ background-position:0% 0%; }
    100%{ background-position:100% 0%; }
}
</style>

@endsection
