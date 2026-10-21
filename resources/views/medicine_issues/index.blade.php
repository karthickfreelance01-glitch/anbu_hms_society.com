@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Header -->
                <div class="d-flex justify-content-between mb-3">
                    <h4>Medicine Billing & Issue</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Medicine Billing & Issue Register</li>
                    </ol>
                </div>

                <!-- Table Card -->
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Medicine Billing & Issue Register</h4>
                        <a href="{{ url('medicine-issues/create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> New Medicine Issue
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="medicineTable" class="table table-bordered table-striped text-center align-middle">
                                <div class="mt-3 text-end">
                                    <h5 class="fw-bold">Overall Pending Amount:
                                        <span id="overallPending" class="text-danger"></span>
                                    </h5>
                                </div>
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Bill ID</th>
                                        <th>Patient Name</th>
                                        <th>Drugs</th>
                                        <th>Drug Count</th>
                                        <th>Price (₹)</th>
                                        <th>Total (₹)</th>
                                        <th>Pending</th>
                                        <th>Issue Date</th>
                                        <th>Issue Time</th>
                                        <th>Payment Details</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($issues as $index => $issue)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $issue->bill_id }}</strong></td>
                                        <td>{{ $issue->patient_name }}</td>

                                        @php
                                            $drugs = $issue->drugs ? explode(', ', $issue->drugs) : [];
                                            $counts = $issue->drug_counts ? explode(', ', $issue->drug_counts) : [];
                                            $prices = $issue->prices ? explode(', ', $issue->prices) : [];
                                        @endphp

                                        <td class="text-start">
                                            <ul class="mb-0 ps-3">
                                                @foreach($drugs as $d)
                                                    <li>{{ $d }}</li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        <td class="text-start">
                                            <ul class="mb-0 ps-3">
                                                @foreach($counts as $c)
                                                    <li>{{ $c }}</li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        <td class="text-start">
                                            <ul class="mb-0 ps-3">
                                                @foreach($prices as $p)
                                                    <li>{{ number_format($p, 2) }}</li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        <td class="text-end">{{ number_format($issue->final_amount, 2) }}</td>
                                        <td class="{{ $issue->pending_payment > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                            {{ number_format($issue->pending_payment, 2) }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d-m-Y') }}</td>
                                        <td>{{ $issue->issue_time }}</td>
                                        <td>
                                            @if($issue->pending_payment != 0)
                                                <button type="button"
                                                    class="btn btn-sm btn-success mb-1 nextPaymentBtn"
                                                    data-bill-id="{{ $issue->bill_id }}"
                                                    data-pending="{{ $issue->pending_payment }}">
                                                    <i class="fas fa-money-bill-wave"></i> Add Payment
                                                </button>
                                            @endif

                                            <button type="button"
                                                class="btn btn-sm btn-primary mb-1 viewPaymentsBtn"
                                                data-bill-id="{{ $issue->bill_id }}">
                                                <i class="fas fa-eye"></i> View Payment
                                            </button>
                                        </td>
                                        <td>
                                            <a href="{{ route('medicine-issues.view', $issue->bill_id) }}"
                                               class="btn btn-sm btn-info mb-1" title="View & Print Bill">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>

                                            <a href="{{ url('medicine-issues/'.$issue->bill_id.'/edit') }}"
                                               class="btn btn-sm btn-warning mb-1" title="Edit Bill">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ url('medicine-issues/'.$issue->bill_id) }}" method="POST"
                                                  class="d-inline deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger mb-1 deleteBtn"
                                                        data-id="{{ $issue->bill_id }}" title="Delete Bill">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="12">No medicine issues found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Next Payment Modal -->
<div class="modal fade" id="nextPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="nextPaymentForm">
            @csrf
            <input type="hidden" name="bill_id" id="modal_bill_id">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add Next Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-2">
                        <label class="form-label fw-bold text-danger">Pending Amount: ₹ <span id="pendingDisplay">0.00</span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Amount (₹)</label>
                        <input type="number" name="next_payment_amount" id="next_payment_amount"
                               class="form-control" required min="1" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="next_payment_date" class="form-control" required>
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
                        <label class="form-label fw-bold">Hospital Account</label>
                        <select name="hospital_account" class="form-select" required>
                            <option value="">-- Select Account --</option>
                            @foreach(DB::table('payment_categories')->get() as $account)
                                <option value="{{ $account->id }}">{{ $account->payment_category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">Add Payment</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Payment Details - <span id="modal_bill_id_text"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <strong>Total Amount:</strong> ₹ <span id="modal_total_amount"></span><br>
                    <strong>Pending Amount:</strong> ₹ <span id="modal_pending_amount"></span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center" id="modalPaymentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Payment Date</th>
                                <th>Amount (₹)</th>
                                <th>Payment Mode</th>
                                <th>Hospital Account</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {

    // ✅ Initialize DataTable
    $('#medicineTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[8, 'desc']],
        columnDefs: [{ orderable: false, targets: [3,4,6,9,10,11] }],
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy','csv','excel','pdf','print'],
        responsive: true
    });

    // ✅ Calculate Overall Pending Total
    let totalPending = 0;
    $('#medicineTable tbody tr').each(function() {
        let pending = parseFloat($(this).find('td:eq(7)').text().replace(/,/g, '')) || 0;
        totalPending += pending;
    });
    $('#overallPending').text(totalPending.toFixed(2));

    // ✅ Delete Confirmation
    $('.deleteBtn').click(function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the bill and all related medicine issues!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });

    // ✅ Open Next Payment Modal
    $('.nextPaymentBtn').click(function() {
        let billId = $(this).data('bill-id');
        let pending = parseFloat($(this).data('pending')) || 0;

        $('#modal_bill_id').val(billId);
        $('#nextPaymentForm').data('pending', pending);
        $('#pendingDisplay').text(pending.toFixed(2));
        $('#next_payment_amount').val('');
        $('#next_payment_amount').attr('max', pending); // restrict typing
        $('#nextPaymentModal').modal('show');
    });

    // ✅ Prevent typing more than pending amount
    $('#next_payment_amount').on('input', function() {
        let pending = parseFloat($('#nextPaymentForm').data('pending')) || 0;
        let val = parseFloat($(this).val()) || 0;
        if (val > pending) {
            $(this).val(pending);
            Swal.fire('Warning', 'You cannot enter more than pending amount ₹' + pending.toFixed(2), 'warning');
        }
    });

    // ✅ Add Next Payment (AJAX)
    $('#nextPaymentForm').submit(function(e) {
        e.preventDefault();
        let pending = parseFloat($(this).data('pending')) || 0;
        let entered = parseFloat($('#next_payment_amount').val()) || 0;

        if (entered <= 0 || entered > pending) {
            Swal.fire('Warning', 'Invalid payment amount!', 'warning');
            return;
        }

        $.ajax({
            url: "{{ route('medicine-issues.addPayment') }}",
            type: "POST",
            data: $(this).serialize(),
            success: res => Swal.fire('Success', res.message, 'success').then(() => location.reload()),
            error: xhr => Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong!', 'error')
        });
    });

    // ✅ View Payment Details
    $('.viewPaymentsBtn').click(function() {
        let billId = $(this).data('bill-id');
        $('#modal_bill_id_text').text(billId);
        $('#modalPaymentsTable tbody').html('<tr><td colspan="5">Loading...</td></tr>');
        $.ajax({
            url: "{{ url('medicine-issues/payments') }}/" + billId,
            type: "GET",
            success: res => {
                $('#modal_total_amount').text(parseFloat(res.total_amount).toFixed(2));
                $('#modal_pending_amount').text(parseFloat(res.pending_amount).toFixed(2));
                let rows = res.payments.length
                    ? res.payments.map((p,i)=>`<tr><td>${i+1}</td><td>${p.payment_date}</td><td>${parseFloat(p.amount).toFixed(2)}</td><td>${p.payment_mode}</td><td>${p.hospital_account}</td></tr>`).join('')
                    : '<tr><td colspan="5">No payments found</td></tr>';
                $('#modalPaymentsTable tbody').html(rows);
                $('#viewPaymentsModal').modal('show');
            },
            error: xhr => Swal.fire('Error', xhr.responseJSON?.message || 'Failed to fetch payments!', 'error')
        });
    });

});
</script>
@endsection
