@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-lg rounded-4 border-0">
                <div class="card-header  text-white">
                    <h4 class="mb-0">Add Escape Record</h4>
                </div>
                <div class="card-body">
                    <form id="escapeForm" action="{{ route('escape.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}" data-name="{{ $p->name }}">
                                            {{ $p->ip_number }} - {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" readonly style="background:#f0f0f0">
                            </div>
                        </div>

                        <!-- Escape Date & Time -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Escape Date <span class="text-danger">*</span></label>
                                <input type="date" name="escape_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Escape Time <span class="text-danger">*</span></label>
                                <input type="time" name="escape_time" class="form-control" required>
                            </div>
                        </div>

                        <!-- File Uploads -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Police FIR Copy <span class="text-danger">*</span></label>
                                <input type="file" name="police_fir_copy" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Video Footage (Optional)</label>
                                <input type="file" name="video_footage" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Additional File (Optional)</label>
                                <input type="file" name="file_attach" class="form-control">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="btnText">Save</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                            <a href="{{ route('escape.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // Initialize Select2
    $('#patient_id').select2({
        placeholder: 'Select Patient',
        allowClear: true,
        width: '100%'
    });

    // Auto-fill patient name
    $('#patient_id').on('change', function(){
        let selected = $(this).find(':selected');
        $('#patient_name').val(selected.data('name') || '');
    });

    // Set default date & time
    const now = new Date();
    const dateStr = now.toISOString().split('T')[0]; // yyyy-mm-dd
    const timeStr = now.toTimeString().split(' ')[0].substring(0,5); // hh:mm
    $('input[name="escape_date"]').val(dateStr);
    $('input[name="escape_time"]').val(timeStr);

    // Form submission with loading button
    $('#escapeForm').submit(function(e){
        e.preventDefault();

        let btn = $('#submitBtn');
        let btnText = $('#btnText');
        let btnLoader = $('#btnLoader');

        btn.prop('disabled', true);
        btnText.text('Saving...');
        btnLoader.removeClass('d-none');

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                Swal.fire('Success!', 'Escape record added successfully.', 'success').then(()=>{
                    window.location.href = "{{ route('escape.index') }}";
                });
            },
            error: function(xhr){
                let msg = xhr.responseJSON?.message || xhr.responseText || 'Something went wrong!';
                Swal.fire('Error!', msg, 'error');
                btn.prop('disabled', false);
                btnText.text('Save');
                btnLoader.addClass('d-none');
            }
        });
    });

});
</script>
@endsection
