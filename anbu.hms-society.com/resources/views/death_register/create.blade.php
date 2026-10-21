@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0 text-success">Death Register</h4>
                </div>
                <div class="card-body">
                    <form id="deathForm" method="POST" action="{{ route('death_register.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">

                            <!-- Patient Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Patient</label>
                                <select name="patient_id" id="patient_id" class="form-control select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}"
                                                data-name="{{ $patient->name }}"
                                                data-phone="{{ $patient->contact_number }}"
                                                data-ip="{{ $patient->ip_number }}">
                                            {{ $patient->ip_number }} - {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Patient Name -->
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" readonly style="background:#f5f5f5">
                            </div>

                            <!-- Death Date & Time -->
                            <div class="col-md-6">
                                <label class="form-label">Death Date</label>
                                <input type="date" name="death_date" id="death_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Death Time</label>
                                <input type="text" name="death_time" id="death_time" class="form-control" placeholder="hh:mm AM/PM" required value="{{ date('h:i A') }}">
                            </div>

                            <!-- Report Attachment -->
                            <div class="col-md-6">
                                <label class="form-label">Report Attachment</label>
                                <input type="file" name="report_attachment" class="form-control">
                            </div>

                            <!-- Note -->
                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea name="note" class="form-control" rows="3"></textarea>
                            </div>

                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-gradient px-4" id="submitBtn">
                                <span id="btnText">Save</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Plugins -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: #fff;
    border: none;
}
</style>

<script>
$(document).ready(function(){

    // Initialize Select2
    $('#patient_id').select2({ width: '100%' });

    // Auto-fill patient name
    $('#patient_id').on('change', function(){
        var selected = $(this).find(':selected');
        $('#patient_name').val(selected.data('name') || '');
    });

    // Simple validation and AJAX submit
    $('#deathForm').on('submit', function(e){
        e.preventDefault();

        var form = this;
        var btn = $('#submitBtn');
        var btnText = $('#btnText');
        var btnLoader = $('#btnLoader');

        btn.prop('disabled', true);
        btnText.text('Saving...');
        btnLoader.removeClass('d-none');

        var formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: res.message || 'Record saved successfully!'
                }).then(function(){
                    window.location.href = "{{ route('death_register.index') }}";
                });
            },
            error: function(xhr){
                btn.prop('disabled', false);
                btnText.text('Save');
                btnLoader.addClass('d-none');

                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for(let k in errors) errMsg += errors[k][0] + '<br>';
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errMsg });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON.message });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                }
            }
        });

    });

});
</script>
@endsection
