@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4">
                    <h3 class="mb-4 text-success fw-bold text-center">🏥 Patient Billing Form</h3>
                    <div class="align-end">
                        <label class="form-label fw-bold">Bill ID</label>
                        <input type="text" class="form-control text-center" value="{{ $billCode }}"  readonly style="width:25%;">
                    </div>
                    <form id="billingForm" method="POST" action="{{ route('patient.billing.store') }}">
                        @csrf
                        <input type="hidden" class="form-control text-center" value="{{ $billCode }}" name="bill_id" readonly style="width:25%;">
                        <!-- Select Patient -->
                        <div class="mb-3 row">
                             
                            <div class="col-lg-4">
                                <label class="form-label fw-bold">Bill Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="bill_date" id="bill_date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label fw-bold">Select Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" class="form-select select2" required>
                                    <option value="">-- Select Patient --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->ip_number ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Billing Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle" id="billingTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Category</th>
                                        <th>Amount (₹)</th>
                                        <th>Received (₹)</th>
                                        <th>Pending (₹)</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td id="total_amount">0</td>
                                        <td id="total_received">0</td>
                                        <td id="total_pending">0</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Add Row -->
                        <div class="mb-3 mt-2 text-end">
                            <button type="button" id="addRowBtn" class="btn btn-primary">+ Add Category</button>
                        </div>
                        <div class="row">
                             <div class="col-lg-6 mb-3">
                            <label class="form-label">Account type</label>
                            <select name="accounttype" id="" class="form-control" required>
                                    <option value="">-- Select Account type --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Savings-SBI">Savings SBI</option>
                                    <option value="Savings_TMB">Savings TMB</option>
                                    <option value="Current account">Current account</option>
                                    <option value="Others">Others</option>
                                </select>
                        </div>
                        
                        <!-- Payment Mode -->
                            <div class="col-lg-6 mb-3">
                                <label class="form-label fw-bold">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" class="form-select" required>
                                    <option value="">-- Select Mode --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                    <option value="NetBanking">Net Banking</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                       
                        

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-5">Save Billing</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function(){
    $('.select2').select2({ width: '100%' });

    const categories = @json($categories);

    function updateBilling(){
        let totalAmount = 0, totalReceived = 0;

        $('#billingTable tbody tr').each(function(){
            let amount = parseFloat($(this).find('.amount').val()) || 0;
            let received = parseFloat($(this).find('.received').val()) || 0;
            let pending = amount - received;
            $(this).find('.pending').val(pending.toFixed(2));

            totalAmount += amount;
            totalReceived += received;
        });

        $('#total_amount').text(totalAmount.toFixed(2));
        $('#total_received').text(totalReceived.toFixed(2));
        $('#total_pending').text((totalAmount - totalReceived).toFixed(2));
    }

    function addRow(){
        let options = '';
        $.each(categories, function(key, label){
            options += `<option value="${key}">${label}</option>`;
        });

        let row = `<tr>
            <td style="width: 250px;">
                <select name="category[]" class="form-select category select2" required>
                    <option value="">-- Select Category --</option>
                    ${options}
                </select>
            </td>
            <td><input type="number" name="amount[]" class="form-control amount" step="0.01" value="0" required></td>
            <td><input type="number" name="received[]" class="form-control received" step="0.01" value="0"></td>
            <td><input type="number" class="form-control pending" readonly></td>
           <td>
    <textarea name="note[]" class="form-control" rows="2" placeholder="Add note..."></textarea>
</td>

            <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        </tr>`;

        $('#billingTable tbody').append(row);
        $('.select2').select2({ width: '100%' });
        updateBilling();
    }

    $('#addRowBtn').click(addRow);

    $(document).on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
        updateBilling();
    });

    $(document).on('input', '.amount, .received', updateBilling);

    addRow();

    // Submit form with SweetAlert
    $('#billingForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Billing saved successfully!'
                }).then(() => {
                    window.location.href = "{{ route('patient.billing.reports') }}";
                });
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseText
                });
            }
        });
    });
});
</script>
@endsection
