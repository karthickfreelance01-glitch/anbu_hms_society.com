@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            {{-- Success & Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header text-white ">
                    <h4 class="mb-0">Edit Admission</h4>
                </div>

                <div class="card-body">
                    <form id="admissionForm" method="POST" action="{{ route('admissions.update', $admission->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">

                            {{-- Patient --}}
                            <div class="col-md-6">
                                <label for="patient_id" class="form-label">Patient</label>
                                <select name="patient_id" id="patient_id" class="form-control select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->patient_id }}" 
                                            data-name="{{ $patient->full_name }}"
                                            {{ $admission->patient_id == $patient->patient_id ? 'selected' : '' }}>
                                            {{ $patient->patient_id }} - {{ $patient->contact_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="patient_name" class="form-label">Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" readonly style="background:#f8f9fa;" 
                                       value="{{ $admission->patient_name_from_master ?? '' }}">
                            </div>

                            {{-- Disease --}}
                            <div class="col-md-6">
                                <label for="symptoms_title" class="form-label">Disease Title</label>
                                <select name="symptoms_title" id="symptoms_title" class="form-control" required>
                                    <option value="{{ $admission->symptoms_title }}">{{ $admission->symptoms_title }}</option>
                                    @foreach($symptoms->unique('symptoms_title') as $symptom)
                                        <option value="{{ $symptom->symptoms_title }}">{{ $symptom->symptoms_title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Admission Date & Time --}}
                             <div class="col-md-6">
                                <label for="admission_date" class="form-label">Admission Date</label>
                                <input type="date" id="admission_date" name="admission_date" value="{{ \Carbon\Carbon::parse($admission->admission_datetime)->format('Y-m-d') }}" class="form-control" required min="{{ date('Y-m-d') }}">
                                <small class="form-text text-muted mt-2">
                                    Selected Date: <span id="date-output">YYYY-MM-DD</span>
                                </small>
                            </div>

                            {{-- Ward --}}
                            <div class="col-md-6">
                                <label for="ward_no" class="form-label">Ward No</label>
                                <select name="ward_no" class="form-control" required>
                                    @foreach($ward as $w)
                                        <option value="{{ $w->ward_no }}" {{ $admission->ward_no == $w->ward_no ? 'selected' : '' }}>{{ $w->ward_no }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Doctor --}}
                            <div class="col-md-6">
                                <label for="doctor_id" class="form-label">Consultant Doctor</label>
                                <select name="doctor_id" class="form-control select2" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}" {{ $admission->consultant_doctor_id == $doc->id ? 'selected' : '' }}>{{ $doc->staff_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Nurse --}}
                            <div class="col-md-6">
                                <label for="nurse_id" class="form-label">Consultant Nurse</label>
                                <select name="nurse_id" class="form-control select2" >
                                    <option value="">Select Nurse</option>
                                    @foreach($nurses as $n)
                                        <option value="{{ $n->id }}" {{ $admission->consultant_nurse_id == $n->id ? 'selected' : '' }}>{{ $n->staff_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Therapy --}}
                            <div class="col-md-6">
                                <label for="therapy" class="form-label">Therapy</label>
                                <select name="therapy[]" class="form-control select2" multiple required>
                                    @foreach($therapies as $t)
                                        <option value="{{ $t->id }}" {{ in_array($t->id, $selectedTherapyIds ?? []) ? 'selected' : '' }}>{{ $t->therapy_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Duration & Monthly Amount --}}
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Duration (Months)</label>
                                <select name="duration" id="duration" class="form-control" required>
                                    @foreach([1,2,3,4,5,6,12] as $month)
                                        <option value="{{ $month }}" {{ $admission->duration == $month ? 'selected' : '' }}>{{ $month }} Month(s)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="monthly_amount" class="form-label">Monthly Amount</label>
                                <input type="number" name="monthly_amount" id="monthly_amount" class="form-control" value="{{ $admission->monthly_amount }}" required>
                            </div>

                            <div class="col-md-6" style="display:none">
                                <label for="total_amount" class="form-label">Total Amount</label>
                                <input type="text" id="total_amount" class="form-control" value="{{ $admission->duration * $admission->monthly_amount }}" readonly>
                            </div>

                            {{-- Initial Amount --}}
                            <div class="col-md-6">
                                <label for="initial_fees" class="form-label">Initial Amount</label>
                                <input type="number" name="initial_fees" class="form-control" value="{{ $admission->initial_fees }}" required>
                            </div>

                            {{-- Payment Mode --}}
                            <div class="col-md-6">
                                <label for="payment_mode" class="form-label">Payment Mode</label>
                                <select name="payment_mode" class="form-control" required>
                                    <option value="CASH" {{ $admission->payment_mode == 'CASH' ? 'selected' : '' }}>Cash</option>
                                    <option value="CARD" {{ $admission->payment_mode == 'CARD' ? 'selected' : '' }}>Card</option>
                                    <option value="ONLINE" {{ $admission->payment_mode == 'ONLINE' ? 'selected' : '' }}>Online</option>
                                </select>
                            </div>

                            {{-- Payment Status --}}
                            <div class="col-md-6">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-control" required>
                                    <option value="PAID" {{ $admission->payment_status == 'PAID' ? 'selected' : '' }}>Paid</option>
                                    <option value="PENDING" {{ $admission->payment_status == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success">Update Admission</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Scripts --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){

    $('.select2').select2({ placeholder: "Select options" });

    $('#patient_id').on('change', function(){
        let selected = $(this).find(':selected');
        $('#patient_name').val(selected.data('name') || '');
    });

    $('#admission_date').on('input', function(){
        let dateVal = $(this).val();
        $('#date-output').text(dateVal || 'YYYY-MM-DD');
    });

    const duration = $('#duration');
    const monthlyAmount = $('#monthly_amount');
    const totalAmount = $('#total_amount');

    function calculateTotal(){
        const months = parseInt(duration.val()) || 0;
        const monthly = parseFloat(monthlyAmount.val()) || 0;
        totalAmount.val((monthly).toFixed(2));
    }

    duration.on('change', calculateTotal);
    monthlyAmount.on('input', calculateTotal);

});
</script>
@endsection
