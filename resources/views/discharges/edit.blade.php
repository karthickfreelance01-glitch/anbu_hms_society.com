@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card shadow-sm border-0">
                <div class="card-header  text-dark">
                    <h4 class="mb-0">Edit Patient Discharge</h4>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('discharges/update/'.$discharge->id) }}" id="editDischargeForm">
                        @csrf
                        <div class="row g-3">

                            <!-- Patient Info -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Patient ID</label>
                                <input type="text" name="patient_id" class="form-control" readonly value="{{ $discharge->patient_id }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Patient Name</label>
                                <input type="text" class="form-control" readonly value="{{ $discharge->full_name }}">
                            </div>

                            <!-- Admission Info -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Admission Date & Time</label>
                                <input type="text" name="admission_datetime" class="form-control" readonly value="{{ $discharge->admission_datetime }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duration (Months)</label>
                                <select name="duration" id="duration" class="form-select" required>
                                    <option value="{{ $discharge->dur }}">{{ $discharge->dur }} Month(s)</option>
                                    @foreach([1,2,3,4,5,6,12] as $month)
                                        <option value="{{ $month }}">{{ $month }} Month(s)</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Monthly Amount -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Monthly Amount</label>
                                <input type="text" id="monthly_amount" class="form-control" readonly value="{{ $discharge->monthly_amount }}">
                            </div>

                            <!-- Paid Amount -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Paid Amount</label>
                                <input type="text" id="paid_amount" class="form-control" readonly value="{{ $paidAmount }}">
                            </div>

                            <!-- Total Amount -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Total Amount</label>
                                <input type="text" id="total_amount" class="form-control" readonly value="0">
                            </div>

                            <!-- Pending Payment -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pending Payment</label>
                                <h4 id="pending_display" class="p-2 rounded text-white text-center">0</h4>
                                <input type="hidden" name="pending_payment" id="pending_amount" value="0">
                            </div>

                            <!-- Payment Mode -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Mode</label>
                                <select name="payment_mode" class="form-select">
                                    <option value="CASH" {{ $discharge->payment_mode == 'CASH' ? 'selected' : '' }}>Cash</option>
                                    <option value="CARD" {{ $discharge->payment_mode == 'CARD' ? 'selected' : '' }}>Card</option>
                                    <option value="ONLINE" {{ $discharge->payment_mode == 'ONLINE' ? 'selected' : '' }}>Online</option>
                                </select>
                            </div>

                            <!-- Discharge Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Discharge Date</label>
                                <input type="date" id="discharge_date_input" class="form-control" required value="{{ \Carbon\Carbon::parse($discharge->dishcharge_datetime)->format('Y-m-d') }}">
                                <small class="form-text text-muted mt-1">
                                    Selected Date: <span id="date-output">{{ \Carbon\Carbon::parse($discharge->dishcharge_datetime)->format('Y-m-d') }}</span>
                                </small>
                                <input type="hidden" id="dischargeDate" name="discharge_date" value="{{ \Carbon\Carbon::parse($discharge->dishcharge_datetime)->format('Y-m-d') }}">
                            </div>

                            <!-- Discharge Summary -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Discharge Summary</label>
                                <textarea name="note" class="form-control" rows="4">{{ $discharge->note }}</textarea>
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success btn-lg">Update Discharge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    function updatePaymentAmounts(){
        let duration = parseInt($('#duration').val()) || 0;
        let monthlyAmount = parseFloat($('#monthly_amount').val()) || 0;
        let paidAmount = parseFloat($('#paid_amount').val()) || 0;

        let totalAmount = duration * monthlyAmount;
        let pendingAmount = totalAmount - paidAmount;

        $('#total_amount').val(totalAmount.toFixed(2));
        $('#pending_amount').val(pendingAmount.toFixed(2));

        const pendingDisplay = $('#pending_display');
        pendingDisplay.text(pendingAmount.toFixed(2));

        if(pendingAmount > 0){
            pendingDisplay.css({'background-color':'#dc3545', 'color':'#fff'});
        } else {
            pendingDisplay.css({'background-color':'#28a745', 'color':'#fff'});
        }
    }

    updatePaymentAmounts();
    $('#duration').on('change', updatePaymentAmounts);

    // Discharge date handling
    const dateInput = $('#discharge_date_input');
    const dateOutput = $('#date-output');
    const dischargeDate = $('#dischargeDate');

    function updateDischargeDate(){
        const dateVal = dateInput.val();
        dateOutput.text(dateVal || 'YYYY-MM-DD');
        dischargeDate.val(dateVal);
    }

    updateDischargeDate();
    dateInput.on('input', updateDischargeDate);
});
</script>
@endsection
