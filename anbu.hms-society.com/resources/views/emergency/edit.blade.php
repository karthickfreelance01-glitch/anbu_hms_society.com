@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-lg rounded-4 p-4 mt-4">
                <h4 class="fw-bold mb-4 text-primary">✏️ Edit Emergency Record</h4>

                <form id="editEmergencyForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $record->id }}">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ $record->patient_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->reg_no }} - {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $record->date }}" required>
                        </div>

                        <div class="col-6 mb-3">
                            <label class="form-label">Illness</label>
                            <input type="text" name="illness" class="form-control" value="{{ $record->illness }}" required>
                        </div>

                        <div class="col-6 mb-3">
                            <label class="form-label">Doctor</label>
                            <input type="text" name="doctor" class="form-control" value="{{ $record->doctor }}">
                        </div>

                        <div class="col-6 mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3">{{ $record->remarks }}</textarea>
                        </div>
                    </div>
                    

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">💾 Update</button>
                        <a href="{{ route('emergency.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let isSubmitting = false;

    $('#editEmergencyForm').submit(function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm me-2"></span> Updating...');

        $.ajax({
            url: "{{ route('emergency.update', $record->id) }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated Successfully!',
                    text: res.message || 'Emergency record has been updated.',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = "{{ route('emergency.index') }}";
                });
            },
            error: function(xhr) {
                isSubmitting = false;
                submitBtn.prop('disabled', false).html('💾 Update');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for (let key in errors) {
                        errMsg += errors[key][0] + '<br>';
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errMsg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong.' });
                }
            }
        });
    });
});
</script>
@endsection
