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
                        <h2 class="fw-bold text-gradient">🩹 Injury Record Form</h2>
                        <p class="text-muted">Log the details of patient injury and treatment</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="injuryForm">
                        @csrf
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
                            <div class="col-md-2">
                                <label class="form-label">Name</label>
                                <input type="text" id="patient_name" class="form-control" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Age</label>
                                <input type="text" id="age" class="form-control" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Admission Date</label>
                                <input type="text" id="admission_date" class="form-control" readonly>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Injury Date</label>
                                <input type="date" name="injury_date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Injury Time</label>
                                <input type="text" name="injury_time" class="form-control" required     ="12:30 PM">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Incident of Injury</label>
                                <textarea name="incident" class="form-control" rows="2" placeholder="Describe what happened..." required></textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Physician</label>
                            <textarea name="physician" id="" class="form-control" required></textarea>
                            
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 shadow-lg">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $('.select2').select2({ width: '100%' });

    
   // Autofill patient info
    $('#patient_id').change(function(){
        var id = $(this).val();
        if(id){
            $.get("{{ url('injuries/patient') }}/" + id, function(data){
                $('#patient_name').val(data.name);
                $('#age').val(data.age);
                $('#admission_date').val(data.date_of_admission);
            });
        } else {
            $('#patient_name, #age, #admission_date').val('');
        }
    });


    // Submit form
    $('#injuryForm').submit(function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ url('injuries/store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                Swal.fire({ icon: 'success', title: 'Saved!', text: 'Injury record added successfully!' })
                    .then(() => window.location.href = "{{ route('injuries.index') }}");
            },
            error: function(xhr){
                btn.prop('disabled', false).html('Submit');
                if(xhr.status === 422){
                    let msg = '';
                    $.each(xhr.responseJSON.errors, (k,v)=> msg += v[0]+'<br>');
                    Swal.fire({ icon:'error', title:'Validation Error', html:msg });
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:'Something went wrong!' });
                }
            }
        });
    });
});
</script>

<style>
.text-gradient { background: linear-gradient(45deg,#4e73df,#1cc88a); -webkit-background-clip: text; color: transparent; }
.btn-gradient { background: linear-gradient(135deg,#4e73df,#1cc88a); color:white; border:none; transition:0.3s; }
.btn-gradient:hover { transform:scale(1.03); box-shadow:0 0 12px rgba(76,175,80,0.3); }
.section-title { font-weight:700; color:#4e73df; border-left:4px solid #1cc88a; padding-left:10px; }
</style>
@endsection
