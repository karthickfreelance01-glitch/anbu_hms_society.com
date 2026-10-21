@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4 animate__animated animate__fadeIn">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-gradient">💬 Counsellor Note</h2>
                        <p class="text-muted">Record today’s counselling observations for your patient</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="counsellorNoteForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-select select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->ip_number }} - {{ $patient->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Select Date <span class="text-danger">*</span></label>
                                <input type="date" name="note_date" id="note_date" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Select Counsellor <span class="text-danger">*</span></label>
                                <select name="counsellor_id" id="counsellor_id" class="form-select select2" required>
                                    <option value="">Select Counsellor</option>
                                    @foreach ($counsellors as $counsellor)
                                        <option value="{{ $counsellor->id }}">{{ $counsellor->staff_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Counselling Fields -->
                        <div class="section-title mb-3">🧠 Counselling Details</div>
                        <div class="mb-3">
                            <label class="form-label">Assess Problem <span class="text-danger">*</span></label>
                            <textarea name="assess_problem" class="form-control" rows="3" placeholder="Describe the patient's current problem..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Counselling Given <span class="text-danger">*</span></label>
                            <textarea name="counselling_given" class="form-control" rows="3" placeholder="Describe the counselling or guidance provided..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Any additional remarks or observations..."></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 shadow-lg">Submit Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<script>
$(document).ready(function(){
    $('.select2').select2({ width: '100%' });

    // Set today's date
    const today = new Date().toISOString().split('T')[0];
    $('#note_date').val(today);

    $('#counsellorNoteForm').on('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ route('counsellornotes.store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Note Saved',
                    text: 'Counsellor note has been recorded successfully!'
                }).then(() => {
                    window.location.href = "{{ route('counsellornotes.index') }}";
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('Submit Note');
                
                let errorTitle = 'Error';
                let errorMessage = 'Something went wrong!';
            
                // Show Laravel validation errors (422)
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    errorTitle = 'Validation Error';
                    errorMessage = '';
                    for (let key in errors) {
                        errorMessage += `<b>${key.replace('_', ' ')}:</b> ${errors[key][0]}<br>`;
                    }
                }
                // Show unexpected server errors (500)
                else if (xhr.status === 500 && xhr.responseJSON.message) {
                    errorTitle = 'Server Error';
                    errorMessage = xhr.responseJSON.message;
                }
            
                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: errorMessage,
                    confirmButtonColor: '#4e73df'
                });
            }

        });
    });
});
</script>

<style>
.text-gradient { background: linear-gradient(45deg, #4e73df, #1cc88a); -webkit-background-clip: text; color: transparent; }
.btn-gradient { background: linear-gradient(135deg, #4e73df, #1cc88a); color: white; border: none; transition: 0.3s; }
.btn-gradient:hover { transform: scale(1.03); box-shadow: 0 0 12px rgba(76,175,80,0.3); }
.section-title { font-size: 1.2rem; font-weight: 700; color: #4e73df; border-left: 4px solid #1cc88a; padding-left: 10px; }
.form-label { font-weight: 600; color: #333; }
.card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
</style>
@endsection
