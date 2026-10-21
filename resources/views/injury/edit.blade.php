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
                        <h2 class="fw-bold text-gradient">✏️ Edit Injury Record</h2>
                        <p class="text-muted">Update the details of a patient injury</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="editInjuryForm" action="{{ route('injuries.update', $injury->id) }}" method="POST">
                        @csrf
                       
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-select select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ $injury->patient_id == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->ip_number }} - {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Name</label>
                                <input type="text" id="patient_name" class="form-control" value="{{ $injury->patient_name }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Age</label>
                                <input type="text" id="age" class="form-control" value="{{ $injury->age }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Admission Date</label>
                                <input type="text" id="admission_date" class="form-control" value="{{ $injury->admission_date }}" readonly>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Injury Date</label>
                                <input type="date" name="injury_date" value="{{ $injury->injury_date }}" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Injury Time</label>
                                <input type="text" name="injury_time" value="{{ $injury->injury_time }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Incident of Injury</label>
                                <textarea name="incident" class="form-control" rows="2" required>{{ $injury->incident }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Physician</label>
                            <input type="text" name="physician" class="form-control" value="{{ $injury->physician }}" required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 shadow-lg">Update</button>
                            <a href="{{ route('injuries.index') }}" class="btn btn-secondary btn-lg px-4 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $('.select2').select2({ width: '100%' });

    // Auto-fill patient info
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

    // AJAX form submit
    $('#editInjuryForm').submit(function(e){
        e.preventDefault();
        const form = this;
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        $.ajax({
            url: $(form).attr('action'),
            method: "POST",
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Injury record updated successfully!'
                }).then(() => window.location.href = "{{ route('injuries.index') }}");
            },
            error: function(xhr){
                btn.prop('disabled', false).html('Update');

                let msg = 'Something went wrong!';
                if(xhr.status === 422 && xhr.responseJSON?.errors){
                    msg = '';
                    $.each(xhr.responseJSON.errors, (k,v)=> msg += v[0]+'<br>');
                } else if(xhr.responseJSON?.message){
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({ icon:'error', title:'Error', html:msg });
            }
        });
    });
});
</script>

<style>
.text-gradient { background: linear-gradient(45deg,#4e73df,#1cc88a); -webkit-background-clip: text; color: transparent; }
.btn-gradient { background: linear-gradient(135deg,#4e73df,#1cc88a); color:white; border:none; transition:0.3s; }
.btn-gradient:hover { transform:scale(1.03); box-shadow:0 0 12px rgba(76,175,80,0.3); }
</style>

@endsection
