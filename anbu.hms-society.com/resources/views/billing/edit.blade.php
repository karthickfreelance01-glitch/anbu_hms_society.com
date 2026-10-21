@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Billing</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('billing.update', $billing->id) }}" method="POST">
                        @csrf
                      

                        <!-- Patient -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Patient ID</label>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->patient_id }}" 
                                            data-name="{{ $p->full_name }}"
                                            {{ $billing->patient_id == $p->patient_id ? 'selected' : '' }}>
                                            {{ $p->patient_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Patient Name</label>
                                <input type="text" id="patient_name" name="patient_name"
                                    class="form-control" 
                                    value="{{ is_array($billing->patient_name) ? implode(', ', $billing->patient_name) : $billing->patient_name }}" 
                                    readonly>
                            </div>
                        </div>

                        <!-- Doctor -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Doctor</label>
                                <select name="doctor_name" class="form-control" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $d)
                                        <option value="{{ $d->id }}"
                                            {{ $billing->doctor_id == $d->id ? 'selected' : '' }}>
                                            {{ $d->staff_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Fees</label>
                                <input type="number" name="doctor_fees" id="doctor_fees" 
                                    class="form-control" value="{{ $billing->doctor_fees }}" required>
                            </div>
                        </div>

                        <!-- Medication -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Medication Fees</label>
                                <input type="number" name="medication_fees" id="medication_fees"
                                    class="form-control" value="{{ $billing->medication_fees }}" required>
                            </div>
                        </div>

                        <!-- Extra Charges -->
                        <div class="row align-items-end mb-3">
                            <div class="col-md-4">
                                <label>Extra Charge</label>
                                <input type="number" id="extra_charge_input" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label>Note</label>
                                <input type="text" id="extra_charge_note_input" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="add_extra_charge" class="btn btn-primary w-100">+ Extra Charge</button>
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
                                <tbody>
                                    @foreach($billing->extra_charge as $item)
                                    <tr>
                                        <td>{{ $item['charge'] }}</td>
                                        <td>{{ $item['note'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove_charge">Remove</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Hidden Inputs Container -->
                        <div id="extraChargeInputs"></div>

                        <!-- Total Bill -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Total Bill</label>
                                <input type="number" name="total_bill" id="total_bill" class="form-control"
                                    value="{{ $billing->total_bill }}" readonly>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('billing.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let extraCharges = @json($billing->extra_charge ?? []);

// Ensure extraCharges is an array
if (!Array.isArray(extraCharges)) {
    extraCharges = Object.values(extraCharges);
}

// Generate hidden inputs
function updateHiddenInputs() {
    let container = $('#extraChargeInputs');
    container.html('');
    extraCharges.forEach((item, index) => {
        container.append(`
            <input type="hidden" name="extra_charge[${index}]" value="${item.charge}">
            <input type="hidden" name="extra_charge_notes[${index}]" value="${item.note}">
        `);
    });
}

// Calculate total bill
function calculateTotal() {
    let doctor = parseFloat($('#doctor_fees').val()) || 0;
    let med = parseFloat($('#medication_fees').val()) || 0;
    let extraTotal = extraCharges.reduce((sum, item) => sum + parseFloat(item.charge || 0), 0);
    $('#total_bill').val((doctor + med + extraTotal).toFixed(2));
}

// Add new extra charge
$('#add_extra_charge').on('click', function() {
    let charge = parseFloat($('#extra_charge_input').val());
    let note = $('#extra_charge_note_input').val();

    if (isNaN(charge) || charge < 0) {
        alert('Enter a valid charge.');
        return;
    }

    extraCharges.push({charge, note});
    $('#extraChargeTable tbody').append(`
        <tr>
            <td>${charge}</td>
            <td>${note}</td>
            <td><button type="button" class="btn btn-sm btn-danger remove_charge">Remove</button></td>
        </tr>
    `);

    updateHiddenInputs();
    calculateTotal();

    $('#extra_charge_input').val('');
    $('#extra_charge_note_input').val('');
});

// Remove extra charge
$(document).on('click', '.remove_charge', function() {
    let row = $(this).closest('tr');
    let index = row.index();
    extraCharges.splice(index, 1); // Works safely now
    row.remove();
    updateHiddenInputs();
    calculateTotal();
});

// Update patient name on selection
$('#patient_id').on('change', function() {
    let name = $(this).find(':selected').data('name');
    $('#patient_name').val(name || '');
});

// Update total when fees change
$('#doctor_fees, #medication_fees').on('input', calculateTotal);

// Initialize
updateHiddenInputs();
calculateTotal();
</script>
@endsection
