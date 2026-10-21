@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-gradient">✏️ Edit Patient Details</h2>
                        <p class="text-muted">Update the patient's information below</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="editPatientForm" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <!-- Personal Details -->
                        <div class="section-title mb-3">👤 Personal Details</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Registration No <span class="text-danger">*</span></label>
                                <input type="text" name="reg_no" class="form-control" value="{{ $patient->reg_no }}" readonly required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_admission" class="form-control" value="{{ $patient->date_of_admission }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Admission Time <span class="text-danger">*</span></label>
                                <input type="text" name="time_of_admission" class="form-control" value="{{ $patient->time_of_admission }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $patient->name }}" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age" class="form-control" value="{{ $patient->age }}" min="0" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option value="Male" {{ $patient->gender=='Male'?'selected':'' }}>Male</option>
                                    <option value="Female" {{ $patient->gender=='Female'?'selected':'' }}>Female</option>
                                    <option value="Other" {{ $patient->gender=='Other'?'selected':'' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select select2">
                                    <option value="">Select</option>
                                    <option {{ $patient->marital_status=='Single'?'selected':'' }}>Single</option>
                                    <option {{ $patient->marital_status=='Married'?'selected':'' }}>Married</option>
                                    <option {{ $patient->marital_status=='Widowed'?'selected':'' }}>Widowed</option>
                                    <option {{ $patient->marital_status=='Divorced'?'selected':'' }}>Divorced</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control" value="{{ $patient->occupation }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Admitted By</label>
                                <input type="text" name="admitted_by" class="form-control" value="{{ $patient->admitted_by }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" name="contact_number" class="form-control" maxlength="10" 
                                       value="{{ $patient->contact_number }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Referral Source</label>
                                <input type="text" name="referral_source" class="form-control" value="{{ $patient->referral_source }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Address & Aadhar -->
                        <div class="section-title mb-3">📍 Address & ID Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Present Address <span class="text-danger">*</span></label>
                                <textarea name="present_address" class="form-control" rows="2" required>{{ $patient->present_address }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea name="permanent_address" class="form-control" rows="2">{{ $patient->permanent_address }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Aadhar Number</label>
                                <input type="text" name="aadhar_number" class="form-control" value="{{ $patient->aadhar_number }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Upload New Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                @if($patient->photo)
                                    <div class="mt-2">
                                        <img src="{{ asset('uploads/patients/'.$patient->photo) }}" alt="Patient Photo" class="rounded" style="width: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Billing Section -->
                        <!-- Billing Section -->
                        <div class="section-title mb-3">💰 Billing Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monthly Amount (₹)</label>
                                <input type="number" id="total_bill" name="total_bill" class="form-control" step="0.01" min="0" value="{{ $bill->total_amount ?? 0 }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Initial Payment (₹)</label>
                                <input type="number" id="initial_payment" name="initial_payment" class="form-control" step="0.01" min="0" value="{{ $bill->initial_payment ?? 0 }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Pending Amount (₹)</label>
                                <input type="number" id="pending_amount" name="pending_amount" class="form-control" readonly value="{{ $bill->pending_amount ?? 0 }}">
                            </div>
                        </div>

                        @php
                            $accounts = DB::table('payment_categories')->get();
                            $selectedAccount = isset($bill) ? $bill->payment_category : null;
                        @endphp

                        <div class="row mb-4">                    
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Type</label>
                                <select name="hospital_account" id="hospital_account" class="form-select select2" required>
                                    <option value="">-- Select Account --</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $selectedAccount == $account->id ? 'selected' : '' }}>
                                            {{ $account->payment_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-select select2" required>
                                    <option value="">Select</option>
                                    <option value="cash" {{ (isset($bill) && $bill->payment_mode=='cash')?'selected':'' }}>Cash</option>
                                    <option value="card" {{ (isset($bill) && $bill->payment_mode=='card')?'selected':'' }}>Card</option>
                                    <option value="upi" {{ (isset($bill) && $bill->payment_mode=='upi')?'selected':'' }}>UPI</option>
                                    <option value="net_banking" {{ (isset($bill) && $bill->payment_mode=='net_banking')?'selected':'' }}>Net Banking</option>
                                </select>
                            </div>
                        </div>

                        <!-- Bill Items Table -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered table-striped align-middle" id="billingTable">
                                <thead class="table-gradient text-white">
                                    <tr>
                                        <th class="text-center" style="width:5%">#</th>
                                        <th>Bill Title</th>
                                        <th style="width:25%">Amount (₹)</th>
                                        <th class="text-center" style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($bill->split_items) && count($bill->split_items) > 0)
                                        @foreach($bill->split_items as $i => $item)
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>
                                                <td><input type="text" name="bill_title[]" class="form-control" value="{{ $item['title'] }}" required></td>
                                                <td><input type="number" name="amount[]" class="form-control item-amount" step="0.01" min="0" value="{{ $item['amount'] }}" required></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">✖</button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td><input type="text" name="bill_title[]" class="form-control" required></td>
                                            <td><input type="number" name="amount[]" class="form-control item-amount" step="0.01" min="0" required></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">✖</button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mb-4">
                            <button type="button" id="addRow" class="btn btn-outline-primary btn-sm">+ Add Row</button>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 shadow-lg">Update Details</button>
                            <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-lg px-4 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JS + Plugins -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });

    let rowCount = $('#billingTable tbody tr').length;

    // Disable item inputs until Initial Payment is entered
    $('.item-amount').prop('disabled', true);

    $('#addRow').click(function(){
        rowCount++;
        $('#billingTable tbody').append(`
            <tr>
                <td class="text-center">${rowCount}</td>
                <td><input type="text" name="bill_title[]" class="form-control" required></td>
                <td><input type="number" name="amount[]" class="form-control item-amount" step="0.01" min="0" required disabled></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">✖</button></td>
            </tr>
        `);
    });

    $(document).on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
        updateRowNumbers();
        updateItemTotal();
    });

    function updateRowNumbers(){
        $('#billingTable tbody tr').each(function(i){
            $(this).find('td:first').text(i + 1);
        });
    }

    // Monthly Amount & Initial Payment validation
    $('#total_bill').on('input', function(){
        let totalBill = parseFloat($(this).val()) || 0;
        let initial = parseFloat($('#initial_payment').val()) || 0;

        if(initial > totalBill){
            $('#initial_payment').val(totalBill.toFixed(2));
            initial = totalBill;
        }

        // Enable Initial Payment field only if Monthly Amount > 0
        $('#initial_payment').prop('disabled', totalBill <= 0);
        updatePending();
    });

    $('#initial_payment').on('input', function(){
        let totalBill = parseFloat($('#total_bill').val()) || 0;
        let initial = parseFloat($(this).val()) || 0;

        if(initial > totalBill){
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Initial Payment cannot exceed Monthly Amount.'
            });
            $(this).val(totalBill.toFixed(2));
            initial = totalBill;
        }

        // Enable item-amount inputs only after Initial Payment is set
        $('.item-amount').prop('disabled', initial <= 0);
        updatePending();
        updateItemTotal();
    });

    $(document).on('input', '.item-amount', function(){
        let initial = parseFloat($('#initial_payment').val()) || 0;
        let totalItems = 0, lastInput = null;

        $('.item-amount').each(function(){
            let val = parseFloat($(this).val()) || 0;
            totalItems += val;
            lastInput = $(this);
        });

        if(totalItems > initial){
            Swal.fire({
                icon: 'warning',
                title: 'Exceeded Limit',
                text: 'Bill items total cannot exceed Initial Payment.'
            });
            let excess = totalItems - initial;
            let newVal = parseFloat(lastInput.val()) - excess;
            lastInput.val(newVal > 0 ? newVal.toFixed(2) : 0);
        }
    });

    // Pending Amount = Monthly Amount - Initial Payment
    function updatePending(){
        let totalBill = parseFloat($('#total_bill').val()) || 0;
        let initial = parseFloat($('#initial_payment').val()) || 0;
        $('#pending_amount').val((totalBill - initial).toFixed(2));
    }

    // Initial calculation on page load
    updatePending();

    // AJAX Form submission
    $('#editPatientForm').submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: '{{ url("patients/$patient->id/update") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                Swal.fire('Success','Patient details updated successfully','success')
                .then(()=>{ window.location.href = '{{ route("patients.index") }}'; });
            },
            error: function(xhr){
                let errorMsg = 'Unknown error';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                } else if(xhr.responseText){
                    errorMsg = xhr.responseText;
                }
                Swal.fire('Error', errorMsg, 'error');
                console.error(xhr);
            }
        });
    });
});
</script>
<style>
.text-gradient {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    color: transparent;
}
.table-gradient {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
}
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    transition: 0.3s;
}
.btn-gradient:hover {
    transform: scale(1.03);
    box-shadow: 0 0 12px rgba(76,175,80,0.3);
}
.section-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #4e73df;
    border-left: 4px solid #1cc88a;
    padding-left: 10px;
}
.form-label {
    font-weight: 600;
    color: #333;
}
.table thead th {
    text-align: center;
    vertical-align: middle;
}
</style>
@endsection
