@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Death Record</h4>
                </div>
                <div class="card-body">
                    <form id="deathUpdateForm" action="{{ url('death/update', $death->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Patient ID -->
                            <div class="col-md-6 mb-3">
                                <label>Patient ID</label>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $death->patient_id == $p->id ? 'selected' : '' }}
                                            data-name="{{ $p->name }}">
                                            {{ $p->ip_number }} - {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Patient Name -->
                            <div class="col-md-6 mb-3">
                                <label>Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" value="{{ $death->name }}" readonly style="background:#dfdede">
                            </div>

                            <!-- Death Date & Time -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Death Date & Time</label>
                                <div class="input-group">
                                    <input type="date" name="death_date" class="form-control" required
                                        value="{{ \Carbon\Carbon::parse($death->death_datetime)->format('Y-m-d') }}">
                                    <input type="text" name="death_time" class="form-control" placeholder="hh:mm AM/PM" required
                                        value="{{ \Carbon\Carbon::parse($death->death_datetime)->format('h:i A') }}">
                                </div>
                            </div>

                            <!-- Report Attachment -->
                            <div class="col-md-6 mb-3">
                                <label>Report Attachment</label>
                                <input type="file" name="report_attachment" class="form-control">
                                @if($death->report_attachment)
                                    <br>
                                    <a href="{{ asset('uploads/death_reports/'.$death->report_attachment) }}" target="_blank">View Current Report</a>
                                @endif
                            </div>

                            <!-- Note -->
                            <div class="col-md-12 mb-3">
                                <label>Note</label>
                                <textarea name="note" class="form-control">{{ $death->note }}</textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="btnText">Update Record</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery + SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Auto-fill patient name
    $('#patient_id').change(function() {
        let selected = $(this).find(':selected');
        $('#patient_name').val(selected.data('name') || '');
    });

    // AJAX form submit with loader
    $('#deathUpdateForm').submit(function(e){
        e.preventDefault();

        let btn = $('#submitBtn');
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
            processData: false,
            contentType: false,
            success: function(res){
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.message || 'Record updated successfully!'
                }).then(()=> window.location.href = "{{ url('death') }}");
            },
            error: function(xhr){
                btn.prop('disabled', false);
                btnText.text('Update Record');
                btnLoader.addClass('d-none');

                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errMsg = '';
                    for(let key in errors){ errMsg += errors[key][0] + '<br>'; }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errMsg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText });
                }
            }
        });
    });

    // SweetAlert for session messages
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
    @endif
});
</script>
@endsection
