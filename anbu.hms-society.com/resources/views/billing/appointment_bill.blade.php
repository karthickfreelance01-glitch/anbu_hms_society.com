@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header"><h4>Add Billing</h4></div>
                <div class="card-body">
                    <form action="{{ route('billing.store') }}" method="POST">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="row mb-3">
                            <input type="hidden" class="form-control" name="appointment_id" 
                                   value="{{ $query->id }}" readonly>
                            <div class="col-md-6">
                                <label>Patient Id</label>
                                <input type="text" class="form-control" name="patient_id" 
                                       value="{{ $query->patient_code }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Patient Name</label>
                                <input type="text" id="patient_name" class="form-control" 
                                       value="{{ $query->full_name }}" readonly>
                            </div>
                        </div>

                        <!-- Doctor Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Doctor Name</label>
                                <input type="hidden" value="{{ $query->doctor_id }}" name="doctor_name">
                                <input type="text" class="form-control" value="{{ $query->staff_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Fees</label>
                                <input type="number" name="doctor_fees" id="doctor_fees" class="form-control"
                                       value="{{ $query->doctor_fees ?? '' }}" required>
                            </div>
                        </div>

                        <!-- Medication Fees -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Medication Fees</label>
                                <input type="number" name="medication_fees" id="medication_fees" class="form-control"
                                       value="{{ $query->medication_fees ?? '' }}" required>
                            </div>
                        </div>

                        <!-- Extra Charges -->
                        <div class="row align-items-end mb-3">
                            <div class="col-md-4">
                                <label>Extra Charge</label>
                                <input type="number" id="extra_charge_input" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label>Extra Charge Note</label>
                                <input type="text" id="extra_charge_note_input" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="add_extra_charge" class="btn btn-primary w-100">+ Add</button>
                            </div>
                        </div>

                        <!-- Extra Charges Table -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="extraChargeTable">
                                <thead>
                                    <tr>
                                        <th>Charge</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Hidden Inputs for extra charges -->
                        <input type="hidden" name="extra_charges" id="extra_charges">
                        <input type="hidden" name="extra_charge_notes" id="extra_charge_notes">

                        <!-- Total Bill -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Total Bill</label>
                                <input type="number" name="total_bill" id="total_bill" class="form-control" readonly>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Save Billing</button>
                        <a href="{{ route('billing.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    let extraCharges = [];
    let extraChargeNotes = [];

    // Add extra charge
    $('#add_extra_charge').on('click', function(){
        let charge = parseFloat($('#extra_charge_input').val());
        let note = $('#extra_charge_note_input').val();

        if(isNaN(charge) || charge < 0){
            alert('Enter a valid extra charge');
            return;
        }

        extraCharges.push(charge);
        extraChargeNotes.push(note);

        // Add row in table
        $('#extraChargeTable tbody').append(`
            <tr>
                <td>${charge}</td>
                <td>${note}</td>
                <td><button type="button" class="btn btn-sm btn-danger remove_charge">Remove</button></td>
            </tr>
        `);

        // Update hidden inputs
        $('#extra_charges').val(JSON.stringify(extraCharges));
        $('#extra_charge_notes').val(JSON.stringify(extraChargeNotes));

        // Clear input fields
        $('#extra_charge_input').val('');
        $('#extra_charge_note_input').val('');

        calculateTotal();
    });

    // Remove extra charge
    $(document).on('click', '.remove_charge', function(){
        let index = $(this).closest('tr').index();
        extraCharges.splice(index, 1);
        extraChargeNotes.splice(index, 1);
        $(this).closest('tr').remove();

        $('#extra_charges').val(JSON.stringify(extraCharges));
        $('#extra_charge_notes').val(JSON.stringify(extraChargeNotes));

        calculateTotal();
    });

    // Auto-calculate total
    function calculateTotal(){
        let doctor = parseFloat($('#doctor_fees').val()) || 0;
        let med = parseFloat($('#medication_fees').val()) || 0;
        let extraTotal = extraCharges.reduce((sum, val) => sum + val, 0);
        $('#total_bill').val(doctor + med + extraTotal);
    }

    $('#doctor_fees, #medication_fees').on('input', calculateTotal);

});
</script>
@if(session('whatsapp_link'))
<script>
    window.open("{{ session('whatsapp_link') }}", "_blank");
</script>
@endif
@endsection
