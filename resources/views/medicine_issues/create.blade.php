@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg rounded-4 p-4 mt-4 border-0">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">🏥 Hospital Billing & Medicine Issue</h2>
                        <hr class="mt-2 mb-4" style="border-top: 3px solid #007bff; width: 200px; margin:auto;">
                    </div>

                    <form id="medicineForm" method="POST" action="{{ url('medicine-issues') }}">
                        @csrf

                        <!-- Patient Info Section -->
                        <div class="card mb-4 border-0 shadow-sm rounded-4 p-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Patient ID</label>
                                    <select name="patient_id" id="patient_id" class="form-select select2" required>
                                        <option value="">-- Select Patient --</option>
                                        @foreach($patients as $p)
                                            <option value="{{ $p->id }}">{{ $p->reg_no }} - {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Patient Name</label>
                                    <input type="text" id="patient_name" class="form-control" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Admission Date</label>
                                    <input type="text" id="date_of_admission" class="form-control" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Issue Date</label>
                                    <input type="date" id="issue_date" name="issue_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Issue Time</label>
                                    <input type="text" id="issue_time" name="issue_time" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Medicine Table Section -->
                        <div class="card mb-4 border-0 shadow-sm rounded-4 p-3">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="drugTable">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>Drug Name</th>
                                            <th>Price (₹)</th>
                                            <th>Quantity</th>
                                            <th>Total (₹)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:45%;">
                                               <select name="drugs[0][id]" class="form-select drug_select select2" required data-index="0">
                                                    <option value="">-- Select Drug --</option>
                                                    @foreach($drugs as $drug)
                                                        <option value="{{ $drug->id }}" 
                                                                data-price="{{ $drug->selling_price ?? 0 }}" 
                                                                @if($drug->current_stock == 0) disabled style="color:red;" @endif>
                                                            {{ $drug->drug_name }} (Stock: {{ $drug->current_stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="drugs[0][price]" class="form-control selling_price text-end" data-index="0" readonly></td>
                                            <td><input type="number" name="drugs[0][count]" class="form-control drug_count text-center" data-index="0" min="1" required></td>
                                            <td><input type="number" name="drugs[0][total]" class="form-control line_total text-end" data-index="0" readonly></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end">
                                <button type="button" id="addRow" class="btn btn-sm btn-success rounded-pill px-3"><i class="fa fa-plus"></i> Add Medicine</button>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div class="card border-0 shadow-sm rounded-4 p-3">
                            <div class="row g-3">
                                <div class="col-md-3 offset-md-3">
                                    <label class="form-label fw-bold">Overall Total (₹)</label>
                                    <input type="text" id="overall_total" class="form-control text-end fw-bold" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Round Off (₹)</label>
                                    <input type="text" id="round_off" class="form-control text-end" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Total Amount (₹)</label>
                                    <input type="text" id="total_amount" class="form-control text-end fw-bold text-success" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Account Type</label>
                                    <select name="hospital_account_id" class="form-select select2" data-placeholder="Select Hospital Account">
                                        <option value="">-- All Accounts --</option>
                                        @foreach($hospitalAccounts as $id => $name)
                                            <option value="{{ $id }}" {{ ($hospital_account_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode" class="form-select" required>
                                        <option value="">-- Select --</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Initial Payment (₹)</label>
                                    <input type="number" name="initial_payment" id="initial_payment" class="form-control text-end" min="0" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Pending Payment (₹)</label>
                                    <input type="text" id="pending_payment" class="form-control text-end fw-bold text-danger" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" id="submitBtn" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i>Save & Generate Bill
                            </button>
                            <a href="{{ url('medicine-issues') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function(){
    $('.select2').select2({ placeholder: 'Select an option', allowClear: true });

    // Fetch patient details
    $('#patient_id').change(function(){
        let id = $(this).val();
        if(id){
            $.get("{{ url('get-patient') }}/" + id, function(data){
                $('#patient_name').val(data.name);
                $('#date_of_admission').val(data.date_of_admission);
            });
        } else {
            $('#patient_name, #date_of_admission').val('');
        }
    });

    // Auto date/time
    const formatAMPM = d => {
        let h = d.getHours(), m = d.getMinutes();
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        m = m < 10 ? '0'+m : m;
        return `${h}:${m} ${ampm}`;
    };
    const updateDateTime = () => {
        let now = new Date();
        $('#issue_time').val(formatAMPM(now));
        $('#issue_date').val(now.toISOString().split('T')[0]);
    };
    updateDateTime();

    let rowIndex = 1;

    const recalcLineTotal = (index) => {
        let price = parseFloat($(`[data-index=${index}].selling_price`).val()) || 0;
        let qty = parseFloat($(`[data-index=${index}].drug_count`).val()) || 0;
        let total = price * qty;
        $(`[data-index=${index}].line_total`).val(total.toFixed(2));
        updateOverallTotal();
    };

    const updateOverallTotal = () => {
        let total = 0;
        $('.line_total').each(function(){ total += parseFloat($(this).val()) || 0; });
        $('#overall_total').val(total.toFixed(2));
        let rounded = Math.round(total);
        $('#round_off').val((rounded - total).toFixed(2));
        $('#total_amount').val((total + (rounded - total)).toFixed(2));
        let initPay = parseFloat($('#initial_payment').val()) || 0;
        $('#pending_payment').val((rounded - initPay).toFixed(2));
    };

    // Drug select + qty change
    $(document).on('change', '.drug_select', function(){
        let index = $(this).data('index');
        let price = parseFloat($(this).find(':selected').data('price')) || 0;
        $(`[data-index=${index}].selling_price`).val(price);
        recalcLineTotal(index);
    });
    $(document).on('input', '.drug_count', function(){
        recalcLineTotal($(this).data('index'));
    });

    // Add row
    $('#addRow').click(function(){
        let row = `<tr>
            <td style="width:45%;">
                <select name="drugs[${rowIndex}][id]" class="form-select drug_select select2" data-index="${rowIndex}" required>
                    <option value="">-- Select Drug --</option>
                    @foreach($drugs as $drug)
                        <option value="{{ $drug->id }}" data-price="{{ $drug->selling_price ?? 0 }}" @if($drug->current_stock == 0) disabled style="color:red;" @endif>
                            {{ $drug->drug_name }} (Stock: {{ $drug->current_stock }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="drugs[${rowIndex}][price]" class="form-control selling_price text-end" data-index="${rowIndex}" readonly></td>
            <td><input type="number" name="drugs[${rowIndex}][count]" class="form-control drug_count text-center" data-index="${rowIndex}" min="1"></td>
            <td><input type="number" name="drugs[${rowIndex}][total]" class="form-control line_total text-end" data-index="${rowIndex}" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="fa fa-trash"></i></button></td>
        </tr>`;
        $('#drugTable tbody').append(row);
        $('.select2').select2({ placeholder: 'Select an option', allowClear: true });
        rowIndex++;
    });

    // Remove row
    $(document).on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
        updateOverallTotal();
    });

    // Update payment
    $('#initial_payment').on('input', updateOverallTotal);

    // Prevent duplicate submission
    $('#medicineForm').on('submit', function() {
        let btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Processing...');
    });
});
</script>
@endsection
