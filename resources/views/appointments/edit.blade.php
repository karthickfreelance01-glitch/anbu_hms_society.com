@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    
    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">New Appointment</h4>
                </div>
                <div class="card-body">

                <form id="appointmentForm" action="{{ url('appointments/update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <!-- Token No -->
                    <div class="col-md-6 mb-3">
                        <label>Token No</label>
                        <input type="text" name="token_no" class="form-control" value="{{ $appointment->token_no }}" readonly style="background: #dfdede">
                    </div>

                    <!-- Patient ID -->
                    <div class="col-md-6 mb-3">
                        <label>Patient ID</label>
                        <select name="patient_id" id="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->patient_id }}" 
                                    data-name="{{ $patient->full_name }}" 
                                    {{ $appointment->patient_id == $patient->patient_id ? 'selected' : '' }}>
                                    {{ $patient->patient_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Patient Name -->
                    <div class="col-md-6 mb-3">
                        <label>Patient Name</label>
                        <input type="text" name="patient_name" id="patient_name" class="form-control" value="{{ $appointment->patient_name }}" readonly>
                    </div>

                    <!-- Symptoms Type -->
                    <div class="col-md-6 mb-3">
                        <label>Symptoms Type</label>
                        <select name="symptoms_type" id="symptoms_type" class="form-control" required>
                            <option value="">Select Type</option>
                            @foreach($symptoms->unique('symptoms_type') as $symptom)
                                <option value="{{ $symptom->symptoms_type }}" 
                                    {{ $appointment->symptoms_type == $symptom->symptoms_type ? 'selected' : '' }}>
                                    {{ $symptom->symptoms_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Symptoms Title -->
                    <div class="col-md-6 mb-3">
                        <label>Symptoms Title</label>
                        <select id="symptoms_title" name="symptoms_title" class="form-control" required>
                            <option value="{{ $appointment->symptoms_title }}" selected>{{ $appointment->symptoms_title }}</option>
                        </select>
                    </div>

                    <!-- Doctor -->
                    <div class="col-md-6 mb-3">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ $doctor->id == $appointment->doctor_id ? 'selected' : '' }}>
                                    {{ $doctor->staff_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Doctor Fees -->
                    <div class="col-md-6 mb-3">
                        <label>Doctor Fees</label>
                        <input type="number" name="doctor_fees" class="form-control" value="{{ $appointment->doctor_fees }}">
                    </div>

                    <!-- Appointment Date & Time -->
                    <div class="col-md-6 mb-3">
                        <label>Appointment Date & Time</label>
                        <input type="datetime-local" name="appointment_datetime" class="form-control" 
                            value="{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('Y-m-d\TH:i') }}">
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-6 mb-3">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="CASH" {{ $appointment->payment_method == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="CARD" {{ $appointment->payment_method == 'CARD' ? 'selected' : '' }}>Card</option>
                            <option value="ONLINE" {{ $appointment->payment_method == 'ONLINE' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div class="col-md-6 mb-3">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="PAID" {{ $appointment->payment_status == 'PAID' ? 'selected' : '' }}>Paid</option>
                            <option value="PENDING" {{ $appointment->payment_status == 'PENDING' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="col-12 mb-3">
                        <label>Message</label>
                        <textarea name="message" class="form-control">{{ $appointment->message }}</textarea>
                    </div>

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                    
                </div>
            </form>





                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let symptoms = @json($symptoms);

// Update Patient Name on change
$('#patient_id').change(function() {
    let name = $(this).find(':selected').data('name');
    $('#patient_name').val(name);
});

// Update Symptoms Titles on type change
$('#symptoms_type').change(function() {
    let type = $(this).val();
    let titles = symptoms.filter(s => s.symptoms_type === type).map(s => s.symptoms_title);

    let $titleSelect = $('#symptoms_title');
    $titleSelect.empty().append('<option value="">Select Title</option>');
    titles.forEach(title => {
        $titleSelect.append(`<option value="${title}">${title}</option>`);
    });
});
</script>

<script>
$('#appointmentForm').submit(function(e) {
    e.preventDefault(); // prevent normal submit

    let form = $(this);
    let url = form.attr('action'); // ✅ get the action attribute
    let formData = form.serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Appointment updated successfully!',
                confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = "{{ url('appointment/view') }}";

            });
        },
        error: function(xhr) {
            let errorMessage = xhr.responseJSON?.message || 'Something went wrong!';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        }
    });
});
</script>


@endsection
