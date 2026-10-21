@php
use Carbon\Carbon;
@endphp

@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border-0 rounded-4 p-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center rounded-3 mb-3">
                                <h4 class="mb-0 fw-bold text-primary">Patient Bill Reports</h4>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle" id="billingTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>IP No</th>
                                                <th>Patient</th>
                                                <th>Admission Date</th>
                                                <th>Bill Summary</th>
                                                <th>Total Paid (â‚¹)</th>
                                                <th>Pending (â‚¹)</th>
                                                <th>History</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reports as $bill)
                                            @php
                                                $admissionDate = Carbon::parse($bill->date_of_admission);
                                                $pending = $bill->total_billed - $bill->total_paid;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bill->ip_number }}</td>
                                                <td>{{ $bill->patient_name }}</td>
                                                <td>{{ $admissionDate->format('d-m-Y') }}</td>
                                                <td>
                                                    @if(!empty($bill->filled_months))
                                                        <ul class="list-unstyled small mb-0">
                                                            @foreach($bill->filled_months as $month)
                                                                <li>
                                                                    <b>{{ $month['month_label'] }}</b>  
                                                                    {{ number_format($month['amount'], 2) }}
                                                                    <small class="text-muted">
                                                                        ({{ $month['period'] }})
                                                                    </small>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">No monthly data</span>
                                                    @endif
                                                    <div class="mt-1 text-info small fw-semibold">
                                                         {{ $bill->months_stayed }} month{{ $bill->months_stayed != 1 ? 's' : '' }}
                                                        @if($bill->extra_days > 0)
                                                            + {{ $bill->extra_days }} day{{ $bill->extra_days != 1 ? 's' : '' }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td><b>{{ number_format($bill->total_paid, 2) }}</b></td>
                                                <td class="{{ $pending > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                    {{ number_format($pending, 2) }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-info btn-sm"
                                                                onclick="showPaymentHistoryModal('{{ $bill->patient_name }}', '{{ $bill->ip_number }}', '{{ $bill->payments ?? '[]' }}')">
                                                            View
                                                        </button>
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-sm" onclick="showAddPaymentModal({{ $bill->patient_id }})">
                                                        Add Payment
                                                    </button>
                                                    <button class="btn btn-warning btn-sm" onclick="showMonthlyModal({{ $bill->patient_id }})">
                                                        Add Monthly Bill
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- card-body -->
                        </div><!-- card -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- âœ… Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addPaymentForm">
            @csrf
            <input type="hidden" name="patient_id" id="modal_patient_id">

            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Received Amount (â‚¹)</label>
                        <input type="number" step="0.01" name="received_amount" id="received_amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Mode</label>
                        <select name="payment_mode" class="form-select" required>
                            <option value="">-- Select Mode --</option>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="UPI">UPI</option>
                            <option value="NetBanking">Net Banking</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Enter remarks (optional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">Save Payment</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- âœ… Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Payment History</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount (₹)</th>
                            <th>Mode</th>
                            <th>Remarks</th>
                            <th>Print</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="paymentHistoryBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- âœ… Print Template (hidden) -->
<div id="printTemplate" style="display:none;">
    <div style="font-family:'Segoe UI',Arial; padding:25px; border:1px solid #ccc; border-radius:10px;">
       <div style="text-align:center;">
            <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Logo" style="height:80px;">
            <h2 style="margin:5px 0;color:#026b07;"> Anbu De-Addiction Rehabilitation Centre</h2>
            <p> 178, Kamarajar Salai, Karaikal, Puducherry 609602<br>
           PH: +91 87789 76391 </p>
        </div>
        <hr style="border:1px solid #007bff;margin:15px 0;">
        <h4 style="text-align:center;text-decoration:underline;">Payment Receipt</h4>
        <table style="width:100%;margin-top:10px;font-size:15px;">
            <tr>
                <td><strong>Patient Name:</strong></td>
                <td id="printPatientName"></td>
                <td><strong>IP Number:</strong></td>
                <td id="printIpNumber"></td>
            </tr>
            <tr>
                <td><strong>Payment Date:</strong></td>
                <td id="printDate"></td>
                <td><strong>Payment Mode:</strong></td>
                <td id="printMode"></td>
            </tr>
            <tr>
                <td><strong>Amount Paid (â‚¹):</strong></td>
                <td colspan="3" id="printAmount" style="font-weight:bold;font-size:16px;color:#28a745;"></td>
            </tr>
            <tr>
                <td><strong>Remarks:</strong></td>
                <td colspan="3" id="printRemarks"></td>
            </tr>
        </table>
        <hr style="border:1px dashed #aaa;margin:15px 0;">
        <div style="margin-top:40px;display:flex;justify-content:space-between;font-size:14px;">
            <div>
                <p>__________________________<br><strong>Authorized Signature</strong></p>
            </div>
        </div>
        <hr style="border:1px solid #007bff;">
       <p style="text-align:center;font-size:13px;margin:5px 0;">Thank you for choosing <strong>Anbu De-Addiction Rehabilitation Centre</strong>. Wishing you good health!</p>
    </div>
</div>
<!-- ✅ Monthly Bill Modal (only one clean version) -->
<div class="modal fade" id="addMonthlyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addMonthlyForm">
            @csrf
            <input type="hidden" name="patient_id" id="monthly_patient_id">

            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-semibold">🧾 Manage Monthly Bills</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Month Period</label>
                        <select name="monthly_period" id="monthly_period" class="form-select" required>
                            <option value="">-- Select Period --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₹)</label>
                        <input type="number" name="monthly_amount" class="form-control" required min="0" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-warning px-4 mb-3">Add Monthly Bill</button>

                    <hr>
                    <h6 class="fw-bold text-primary mb-3">Existing Monthly Bills</h6>
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Period</th>
                                <th>Amount (₹)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="existingMonthlyBills"></tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 🟢 Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPaymentForm">
            @csrf
            <input type="hidden" name="ip_number" id="edit_ip_number">
            <input type="hidden" name="payment_index" id="edit_payment_index">

            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Edit Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" id="edit_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mode</label>
                        <select name="mode" id="edit_mode" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="UPI">UPI</option>
                            <option value="NetBanking">Net Banking</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks</label>
                        <input type="text" name="remarks" id="edit_remarks" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">Update</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- âœ… Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function(){
    $('#billingTable').DataTable({ order: [[3, 'desc']], pageLength: 10 });
});

// âœ… Show Add Payment Modal
function showAddPaymentModal(id){
    $('#modal_patient_id').val(id);
    $('#received_amount').val('');
    $('#addPaymentModal').modal('show');
}

// âœ… Save Payment
$('#addPaymentForm').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: "{{ url('billing/add-payment') }}",
        method: "POST",
        data: $(this).serialize(),
        success: res => Swal.fire({icon:'success',title:'Payment Added',text:res.message}).then(()=>location.reload()),
        error: xhr => Swal.fire({icon:'error',title:'Error',text:xhr.responseText})
    });
});

// âœ… Show Monthly Modal
// âœ… Show Monthly Modal with automatic month options
function showMonthlyModal(patientId) {
    $('#monthly_patient_id').val(patientId);

    $.ajax({
        url: "{{ url('billing/get-monthly-bills') }}/" + patientId, // backend returns stored JSON
        method: "GET",
        success: function (res) {
            const bills = res.monthly_bills ? JSON.parse(res.monthly_bills) : [];
            const options = generateMonthOptions(bills);
            $('#monthly_period').html(options);
            $('#addMonthlyModal').modal('show');
        },
        error: function (xhr) {
            console.error(xhr);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not fetch billing data' });
        }
    });
}

// âœ… Generate month options up to current date
function generateMonthOptions(bills) {
    let lastToDate = null;
    if (bills.length > 0) {
        const lastBill = bills[bills.length - 1];
        lastToDate = new Date(lastBill.to_date);
    }

    const today = new Date();
    let startDate = lastToDate ? new Date(lastToDate) : null;
    const options = ['<option value="">-- Select Period --</option>'];

    let count = bills.length;
    while (startDate && startDate < today) {
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + 1);

        const label = ordinal(++count) + " Month ” (" + 
            formatDate(startDate) + " to " + formatDate(endDate) + ")";
        
        options.push(`<option value="${formatDate(startDate)}|${formatDate(endDate)}">${label}</option>`);

        startDate = endDate;
        if (endDate >= today) break;
    }

    // If partial extra days exist
    if (startDate && startDate < today) {
        options.push(`<option value="${formatDate(startDate)}|${formatDate(today)}">Extra Days â€” (${formatDate(startDate)} to ${formatDate(today)})</option>`);
    }

    return options.join('');
}

// âœ… Helpers
function formatDate(date) {
    const d = new Date(date);
    return d.toISOString().split('T')[0];
}

function ordinal(n) {
    const s = ["th", "st", "nd", "rd"], v = n % 100;
    return n + (s[(v - 20) % 10] || s[v] || s[0]);
}


// âœ… Save Monthly Bill
$('#addMonthlyForm').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: "{{ url('billing/add-monthly') }}",
        method: "POST",
        data: $(this).serialize(),
        success: res => Swal.fire({icon:'success',title:'Monthly Bill Added',text:res.message}).then(()=>location.reload()),
        error: xhr => Swal.fire({icon:'error',title:'Error',text:xhr.responseText})
    });
});

// âœ… Payment History
function showPaymentHistoryModal(name, ip, paymentsJson){
    const payments = JSON.parse(paymentsJson || '[]');
    const rows = payments.length ? payments.map(p => `
        <tr>
            <td>${p.date}</td>
            <td>â‚¹${p.amount}</td>
            <td>${p.mode}</td>
            <td><button class="btn btn-sm btn-outline-primary" onclick="printBill('${name}','${ip}','${p.amount}','${p.date}','${p.mode}','${p.remarks||''}')">Print</button></td>
        </tr>`).join('') :
        `<tr><td colspan="4" class="text-center text-muted">No payments found</td></tr>`;
    $('#paymentHistoryBody').html(rows);
    $('#paymentHistoryModal').modal('show');
}

// âœ… Print Receipt
function printBill(name, ip, amount, date, mode, remarks){
    $('#printPatientName').text(name);
    $('#printIpNumber').text(ip);
    $('#printDate').text(date);
    $('#printMode').text(mode);
    $('#printAmount').text(parseFloat(amount).toFixed(2));
    $('#printRemarks').text(remarks);
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`<html><head><title>Payment Receipt</title></head><body>${$('#printTemplate').html()}</body></html>`);
    printWindow.document.close();
    printWindow.print();
}
function editPayment(ipNumber, index) {
    $.ajax({
        url: "{{ url('billing/get-payment') }}/" + ipNumber + "/" + index,
        method: "GET",
        success: function(res) {
            $('#edit_ip_number').val(ipNumber);
            $('#edit_payment_index').val(index);
            $('#edit_date').val(res.date);
            $('#edit_amount').val(res.amount);
            $('#edit_mode').val(res.mode);
            $('#edit_remarks').val(res.remarks);
            $('#editPaymentModal').modal('show');
        },
        error: function() {
            Swal.fire({icon: 'error', title: 'Error', text: 'Failed to load payment details'});
        }
    });
}
function showPaymentHistoryModal(name, ip, paymentsJson){
    const payments = JSON.parse(paymentsJson || '[]');

    const rows = payments.length ? payments.map((p, index) => `
        <tr>
            <td>${p.date}</td>
            <td>₹${parseFloat(p.amount).toFixed(2)}</td>
            <td>${p.mode}</td>
            <td>${p.remarks || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary"
                    onclick="printBill('${name}','${ip}','${p.amount}','${p.date}','${p.mode}','${p.remarks||''}')">
                    Print
                </button>
            </td>
            <td>
                <button class="btn btn-sm btn-success" 
                        onclick="editPayment('${ip}', ${index})">Edit</button>
                <button class="btn btn-sm btn-danger"
                        onclick="deletePayment('${ip}', ${index})">Delete</button>
            </td>
        </tr>
    `).join('') :
    `<tr><td colspan="6" class="text-center text-muted">No payments found</td></tr>`;

    $('#paymentHistoryBody').html(rows);
    $('#paymentHistoryModal').modal('show');
}


$('#editPaymentForm').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: "{{ url('billing/update-payment') }}",
        method: "POST",
        data: $(this).serialize(),
        success: res => Swal.fire({icon:'success',title:'Updated',text:res.message}).then(()=>location.reload()),
        error: xhr => Swal.fire({icon:'error',title:'Error',text:xhr.responseText})
    });
});

function deletePayment(ipNumber, index) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This payment record will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('billing/delete-payment') }}",
                method: "POST",
                data: { _token: "{{ csrf_token() }}", ip_number: ipNumber, index: index },
                success: res => Swal.fire({icon:'success',title:'Deleted',text:res.message}).then(()=>location.reload()),
                error: xhr => Swal.fire({icon:'error',title:'Error',text:xhr.responseText})
            });
        }
    });
}

// ✅ Show Monthly Modal
function showMonthlyModal(patientId) {
    $('#monthly_patient_id').val(patientId);
    $.ajax({
        url: "{{ url('billing/get-monthly-bills') }}/" + patientId,
        method: "GET",
        success: function (res) {
            const bills = res.monthly_bills ? JSON.parse(res.monthly_bills) : [];
            const options = generateMonthOptions(bills);
            $('#monthly_period').html(options);

            let rows = '';
            if (bills.length > 0) {
                bills.forEach((b, i) => {
                    rows += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${b.from_date} to ${b.to_date}</td>
                            <td><input type="number" step="0.01" id="amount_${i}" value="${b.amount}" class="form-control form-control-sm"></td>
                            <td>
                                <button class="btn btn-sm btn-success" onclick="updateMonthlyBill(${patientId}, ${i})">Update</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMonthlyBill(${patientId}, ${i})">Delete</button>
                            </td>
                        </tr>`;
                });
            } else {
                rows = `<tr><td colspan="4" class="text-center text-muted">No monthly bills found</td></tr>`;
            }
            $('#existingMonthlyBills').html(rows);
            $('#addMonthlyModal').modal('show');
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not fetch billing data' });
        }
    });
}

// ✅ Generate month options
function generateMonthOptions(bills) {
    let lastToDate = null;
    if (bills.length > 0) lastToDate = new Date(bills[bills.length - 1].to_date);

    const today = new Date();
    let startDate = lastToDate ? new Date(lastToDate) : null;
    const options = ['<option value="">-- Select Period --</option>'];

    let count = bills.length;
    while (startDate && startDate < today) {
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + 1);
        const label = ordinal(++count) + " Month — (" + formatDate(startDate) + " to " + formatDate(endDate) + ")";
        options.push(`<option value="${formatDate(startDate)}|${formatDate(endDate)}">${label}</option>`);
        startDate = endDate;
        if (endDate >= today) break;
    }

    if (startDate && startDate < today) {
        options.push(`<option value="${formatDate(startDate)}|${formatDate(today)}">Extra Days — (${formatDate(startDate)} to ${formatDate(today)})</option>`);
    }

    return options.join('');
}

function formatDate(date) {
    const d = new Date(date);
    return d.toISOString().split('T')[0];
}

function ordinal(n) {
    const s = ["th","st","nd","rd"], v = n % 100;
    return n + (s[(v-20)%10] || s[v] || s[0]);
}

// ✅ Update / Delete Monthly Bills
function updateMonthlyBill(patientId, index) {
    const amount = $(`#amount_${index}`).val();
    $.ajax({
        url: "{{ url('billing/update-monthly') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            patient_id: patientId,
            index: index,
            amount: amount
        },
        success: res => Swal.fire({ icon:'success', title:'Updated', text:res.message }).then(()=>location.reload()),
        error: xhr => Swal.fire({ icon:'error', title:'Error', text:xhr.responseText })
    });
}

function deleteMonthlyBill(patientId, index) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This monthly record will be removed!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(result => {
        if(result.isConfirmed) {
            $.ajax({
                url: "{{ url('billing/delete-monthly') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    patient_id: patientId,
                    index: index
                },
                success: res => Swal.fire({ icon:'success', title:'Deleted', text:res.message }).then(()=>location.reload()),
                error: xhr => Swal.fire({ icon:'error', title:'Error', text:xhr.responseText })
            });
        }
    });
}

</script>
@endsection
