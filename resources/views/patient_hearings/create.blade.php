@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-4">
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-gradient mb-0">⚖️ Add Patient Hearing Details</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hearings.index') }}">Hearing</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
            </div>

            <!-- Form -->
            <div class="card shadow border-0 rounded-4">
                <div class="card-body">
                    <form id="hearingForm" method="POST" action="{{ route('hearings.store') }}" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Patient Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Patient Name <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-select select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }} (IP: {{ $patient->ip_number }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Auto Fetched Admission Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Admission</label>
                                <input type="text" id="date_of_admission" class="form-control" name="date_of_admission" readonly>
                            </div>

                            <!-- FIR / CSR -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">FIR / CSR  <span class="text-danger">*</span></label>
                                <input type="text" name="fir_csr" id="fir_csr" class="form-control" required>
                            </div>

                            <!-- Date of Hearing -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Hearing <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_hearing" id="date_of_hearing" class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('hearings.index') }}" class="btn btn-secondary">Cancel</a>
                            
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select a patient",
        width: '100%'
    });

    // Auto-fetch admission date on patient select
    $('#patient_id').on('change', function(){
        var patientId = $(this).val();
        if(patientId){
            $.ajax({
                url: "{{ url('patients/get-admission-date') }}/" + patientId,
                type: "GET",
                success: function(response){
                    $('#date_of_admission').val(response.date_of_admission ?? '');
                }
            });
        } else {
            $('#date_of_admission').val('');
        }
    });

    // Form Validation + Success Alert
    $('#hearingForm').on('submit', function(e){
        if(this.checkValidity() === false){
            e.preventDefault();
            e.stopPropagation();
        } else {
            e.preventDefault();
            Swal.fire({
                icon: 'success',
                title: 'Hearing Details Saved!',
                showConfirmButton: false,
                timer: 1500
            });
            setTimeout(() => this.submit(), 1200);
        }
        $(this).addClass('was-validated');
    });
});
</script>

<style>
.text-gradient {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    color: transparent;
}
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    transition: 0.3s;
}
.btn-gradient:hover {
    transform: scale(1.03);
    box-shadow: 0 0 12px rgba(76, 175, 80, 0.3);
}
</style>
@endsection
