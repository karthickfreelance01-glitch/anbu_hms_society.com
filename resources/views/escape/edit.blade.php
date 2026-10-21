@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-lg rounded-4 p-4 mt-3 border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Escape Record</h4>
                </div>

                <div class="card-body">
                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="escapeForm" action="{{ route('escape.update', $escape->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="row mb-3">
                            <!-- Patient ID -->
                            <div class="col-md-6">
                                <label class="form-label">Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">-- Select Patient --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" data-name="{{ $patient->name }}"
                                            {{ $escape->patient_id == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->id }} - {{ $patient->ip_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Patient Name -->
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" 
                                    value="{{ $escape->patient_name }}" readonly style="background:#f0f0f0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Escape Date -->
                            <div class="col-md-6">
                                <label class="form-label">Escape Date <span class="text-danger">*</span></label>
                                <input type="date" name="escape_date" class="form-control"
                                    value="{{ date('Y-m-d', strtotime($escape->escape_datetime)) }}" required>
                            </div>

                            <!-- Escape Time -->
                            <div class="col-md-6">
                                <label class="form-label">Escape Time <span class="text-danger">*</span></label>
                                <input type="time" name="escape_time" class="form-control"
                                    value="{{ date('H:i', strtotime($escape->escape_datetime)) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Police FIR Copy -->
                            <div class="col-md-6">
                                <label class="form-label">Police FIR Copy <span class="text-danger">*</span></label>
                                <input type="file" name="police_fir_copy" class="form-control">
                                @if($escape->police_fir_copy)
                                    <a href="{{ asset($escape->police_fir_copy) }}" target="_blank" class="d-block mt-2 text-primary">
                                        View FIR
                                    </a>
                                @endif
                            </div>

                            <!-- Video Footage -->
                            <div class="col-md-6">
                                <label class="form-label">Video Footage (Optional)</label>
                                <input type="file" name="video_footage" class="form-control">
                                @if($escape->video_footage)
                                    <a href="{{ asset($escape->video_footage) }}" target="_blank" class="d-block mt-2 text-primary">
                                        View Video
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Additional File -->
                            <div class="col-md-6">
                                <label class="form-label">File Attachment (Optional)</label>
                                <input type="file" name="file_attach" class="form-control">
                                @if($escape->file_attach)
                                    <a href="{{ asset($escape->file_attach) }}" target="_blank" class="d-block mt-2 text-primary">
                                        View File
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary" id="updateBtn">
                                <span id="btnText">Update</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                            <a href="{{ route('escape.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- jQuery, Select2, SweetAlert --}}
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
        let name = $(this).find(':selected').data('name');
        $('#patient_name').val(name || '');
    });

    // Update form submission with loading button & SweetAlert
    $('#escapeForm').submit(function(e){
        e.preventDefault();

        let btn = $('#updateBtn');
        let btnText = $('#btnText');
        let btnLoader = $('#btnLoader');

        btn.prop('disabled', true);
        btnText.text('Updating...');
        btnLoader.removeClass('d-none');

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                Swal.fire({
                    title: 'Updated!',
                    text: 'Escape record updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(()=>{
                    window.location.href = "{{ route('escape.index') }}";
                });
            },
            error: function(xhr){
                let msg = xhr.responseJSON?.message || xhr.responseText || 'Something went wrong!';
                Swal.fire('Error!', msg, 'error');
                btn.prop('disabled', false);
                btnText.text('Update');
                btnLoader.addClass('d-none');
            }
        });
    });
});
</script>
@endsection
