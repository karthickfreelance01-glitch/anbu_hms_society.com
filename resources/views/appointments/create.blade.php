@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    
    <div class="page-wrapper">
        <div class="content py-4">
            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-header text-white">
                        <h4 class="mb-0">Book  Appointment</h4>
                    </div>
                    <div class="card-body">

                        <form id="appointmentForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="token_no" class="form-label">Token Number</label>
                                <input type="text" name="token_no" id="token_no" class="form-control" value="{{ $token_no }}" readonly style="background: #f8f9fa;">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_id" class="form-label">Select Patient</label>
                                    <select name="patient_id" id="patient_id" class="form-select" required>
                                        <option value="">-- Select Patient --</option>
                                        @foreach($data['patients'] as $patient)
                                            <option value="{{ $patient->patient_id }}" 
                                                    data-name="{{ $patient->full_name }}" 
                                                    data-phone="{{ $patient->contact_number }}">
                                                {{ $patient->patient_id }} - {{ $patient->full_name }} - {{ $patient->contact_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="patient_name" class="form-label">Patient Name</label>
                                    <input type="text" id="patient_name" class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="patient_phone" class="form-label">Phone Number</label>
                                    <input type="text" id="patient_phone" class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="symptoms_title" class="form-label">Disease / Symptoms</label>
                                    <select name="symptoms_title" id="symptoms_title" class="form-select" required>
                                        <option value="">-- Select Disease --</option>
                                        @foreach($data['symptoms']->unique('symptoms_title') as $symptom)
                                            <option value="{{ $symptom->symptoms_title }}">{{ $symptom->symptoms_title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="doctor_id" class="form-label">Assign Doctor</label>
                                    <select name="doctor_id" id="doctor_id" class="form-select" required>
                                        <option value="">-- Select Doctor --</option>
                                        @foreach($data['doctors'] as $doctor)
                                            <option value="{{ $doctor->id }}">{{ $doctor->staff_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Appointment Date & Time</label>
                                    <div class="input-group">
                                        <input type="date" id="date" class="form-control" required min="{{ date('Y-m-d') }}">
                                        <select id="hour" class="form-select" required></select>
                                        <select id="minute" class="form-select" required></select>
                                        <select id="ampm" class="form-select" required>
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                    <small class="form-text text-muted mt-1">
                                        Selected: <span id="date-output">YYYY-MM-DD</span> at <span id="time-output">HH:MM AM/PM</span>
                                    </small>
                                    <input type="hidden" id="admissionDatetime" name="appointment_datetime" value="">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="message" class="form-label">Doctor Notes</label>
                                    <textarea name="message" id="message" rows="4" class="form-control" placeholder="Any additional notes..."></textarea>
                                </div>

                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Book Appointment</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    $('#patient_id').select2({ placeholder: 'Select Patient', allowClear: true, width: '100%' });

    $('#patient_id').on('change', function(){
        var selected = $(this).find(':selected');
        $('#patient_name').val(selected.data('name') || '');
        $('#patient_phone').val(selected.data('phone') || '');
    });

    for(let i = 1; i <= 12; i++){
        $('#hour').append(`<option value="${i<10?'0'+i:i}">${i<10?'0'+i:i}</option>`);
    }
    for(let i = 0; i < 60; i++){
        $('#minute').append(`<option value="${i<10?'0'+i:i}">${i<10?'0'+i:i}</option>`);
    }

    function to24Hour(hour, ampm){
        hour = parseInt(hour);
        if(ampm === 'AM'){
            if(hour === 12) hour = 0;
        } else {
            if(hour !== 12) hour += 12;
        }
        return hour < 10 ? '0'+hour : hour;
    }

    function updateDatetime(){
        const dateVal = $('#date').val();
        const hourVal = $('#hour').val();
        const minuteVal = $('#minute').val();
        const ampmVal = $('#ampm').val();

        if(dateVal && hourVal && minuteVal && ampmVal){
            const hour24 = to24Hour(hourVal, ampmVal);
            const datetime24 = `${dateVal} ${hour24}:${minuteVal}:00`;
            $('#admissionDatetime').val(datetime24);
            $('#date-output').text(dateVal);
            $('#time-output').text(`${hourVal}:${minuteVal} ${ampmVal}`);
        }
    }

    $('#date, #hour, #minute, #ampm').on('change', updateDatetime);

   $('#appointmentForm').submit(function(e){
    e.preventDefault();
    let formData = $(this).serialize();

    $.post("{{ url('appointments/store') }}", formData, function(response){
        Swal.fire({
            icon: 'success',
            title: 'Appointment Booked!',
            text: response.message,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('appointment/view') }}";
            }
        });
    }).fail(function(xhr){
        let error = xhr.responseJSON;
        Swal.fire('Error!', error?.message || 'Something went wrong', 'error');
    });
});


});
</script>
@endsection
