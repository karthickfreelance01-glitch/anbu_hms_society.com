@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-lg border-0 rounded-4 p-4 mt-4">
                <h4 class="text-center mb-4 fw-bold text-primary">Add Emergency Hospitalization</h4>

                <form id="emergencyForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Patient</label>
                            <select name="patient_id" class="form-select select2" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->reg_no }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Illness</label>
                            <input type="text" name="illness" class="form-control" placeholder="Enter illness details">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Doctor</label>
                            <input type="text" name="doctor" class="form-control" placeholder="Enter doctor name">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Add any remarks..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">💾 Save</button>
                        <a href="{{ url('emergency') }}" class="btn btn-secondary px-4">↩ Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Required JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%', placeholder: 'Select Patient' });

    let isSubmitting = false;

    $('#emergencyForm').submit(function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

        $.ajax({
            url: "{{ route('emergency.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: '✅ Saved Successfully!',
                    text: res.message || 'Emergency hospitalization registered successfully!',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = "{{ route('emergency.index') }}";
                });
            },
            error: function(xhr) {
                isSubmitting = false;
                submitBtn.prop('disabled', false).html('💾 Save');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for (let key in errors) {
                        errMsg += errors[key][0] + '<br>';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error!',
                        html: errMsg,
                        confirmButtonColor: '#d33'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again later.',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function() {
                isSubmitting = false;
            }
        });
    });
});
</script>
@endsection
