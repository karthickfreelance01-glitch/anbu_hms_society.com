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
                        <h2 class="fw-bold text-gradient">🩺 Doctor Note Form</h2>
                        <p class="text-muted">Record today’s observations for your patient</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="doctorNoteForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-select select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->ip_number }} - {{ $patient->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="note_date" id="note_date" class="form-control">
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Observation Fields -->
                        <div class="section-title mb-3">📋 Patient Observations</div>
                        <div class="row g-3">
                            <div class="col-md-2"><label class="form-label">Height (cm)</label><input type="text" name="height" class="form-control" placeholder="e.g. 170"></div>
                            <div class="col-md-2"><label class="form-label">Weight (kg)</label><input type="text" name="weight" class="form-control" placeholder="e.g. 65"></div>
                            <div class="col-md-2"><label class="form-label">BP (mmHg)</label><input type="text" name="bp" class="form-control" placeholder="e.g. 120/80"></div>
                            <div class="col-md-2"><label class="form-label">PR (bpm)</label><input type="text" name="pr" class="form-control" placeholder="e.g. 75"></div>
                            <div class="col-md-2"><label class="form-label">Temp (°F)</label><input type="text" name="temp" class="form-control" placeholder="e.g. 98.6"></div>
                            <div class="col-md-2"><label class="form-label">SpO₂ (%)</label><input type="text" name="spo2" class="form-control" placeholder="e.g. 98"></div>
                        </div>

                        <hr class="my-4">

                        <!-- Notes Section -->
                        <div class="section-title mb-3">📝 Doctor’s Notes</div>
                        <div class="mb-4">
                            <label class="form-label">Last Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Write observations or treatment notes here..."></textarea>
                        </div>

                        <!-- Document Upload -->
                        <div class="section-title mb-3">📎 Attach Supporting Document</div>
                        <div class="mb-4">
                            <label class="form-label">Upload Document (Optional)</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Allowed formats: PDF, JPG, PNG, DOC </small>
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

    const today = new Date().toISOString().split('T')[0];
    $('#note_date').val(today);

    $('#doctorNoteForm').on('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ route('doctor.notes.store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Note Saved',
                    text: 'Doctor note has been recorded successfully!'
                }).then(() => {
                    window.location.href = "{{ route('doctornotes.index') }}";
                });
            },
            error: function(xhr){
                submitBtn.prop('disabled', false).html('Submit Note');
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let msg = '';
                    for(let key in errors){ msg += errors[key][0] + '<br>'; }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: msg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong!' });
                }
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
