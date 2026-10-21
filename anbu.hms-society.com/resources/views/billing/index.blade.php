@php
use Carbon\Carbon;
@endphp

@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <h3 class="mb-4 text-success fw-bold">Patient Bills Report</h3>

                <div class="card shadow-lg border-0 rounded-4 p-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="billing">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Reg No</th>
                                        <th>Patient Name</th>
                                        <th>Date of Admission</th>
                                        <th>Total Amount (₹)</th>
                                        <th>Initial Payment (₹)</th>
                                        <th>Pending Amount (₹)</th>
                                        <th>Bill Date</th>
                                        <th>Payment Details</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bills as $bill)
                                        @if($bill->discharge_status !== 'Discharged')
                                            @php
                                                $splitItems = json_decode($bill->split_items, true) ?? [];
                                                $nextPayments = json_decode($bill->next_payments, true) ?? [];
                                                $nextTotal = array_sum(array_column($nextPayments, 'amount'));

                                                // Months since admission
                                                $admissionDate = Carbon::parse($bill->date_of_admission);
                                                $currentDate = Carbon::now();
                                                $monthsDiff = max(1, $admissionDate->diffInMonths($currentDate)); // at least 1 month

                                                // Total amount = months * base amount
                                                $totalAmount = $monthsDiff * $bill->total_amount;

                                                // Pending amount
                                                $pending = $totalAmount - $bill->initial_payment - $nextTotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bill->reg_no }}</td>
                                                <td>{{ $bill->name }}</td>
                                                <td>{{ $bill->date_of_admission }}</td>
                                                <td>₹{{ number_format($totalAmount, 2) }} ({{ $monthsDiff }} month(s) × ₹{{ number_format($bill->total_amount, 2) }})</td>
                                                <td>₹{{ number_format($bill->initial_payment, 2) }}</td>
                                                <td class="{{ $pending > 0 ? 'text-danger' : 'text-success' }}">
                                                    ₹{{ number_format($pending, 2) }}
                                                </td>
                                                <td>{{ $bill->bill_date }}</td>

                                                <!-- Payment Details Modal -->
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $bill->id }}">
                                                        View
                                                    </button>
                                                    <div class="modal fade" id="paymentModal{{ $bill->id }}" tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-info text-white">
                                                                    <h5 class="modal-title">Payment Details</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body row">
                                                                    <div class="col-6">
                                                                        <h6 class="fw-bold text-primary">Initial Payment</h6>
                                                                    <p>₹{{ number_format($bill->initial_payment, 2) }}</p>
                                                                    </div>
                                                                    
                                                                    <div class=" col-6 text-end">
                                                                        <h6 class="fw-bold text-primary "> Payment Date</h6>
                                                                        <p>{{ $bill->date_of_admission }}</p>
                                                                    </div>
                                                                    

                                                                    @if(count($splitItems) > 0)
                                                                    <h6 class="fw-bold text-primary mt-3">Split Items</h6>
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Item</th>
                                                                                <th>Amount (₹)</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($splitItems as $item)
                                                                                <tr>
                                                                                    <td>{{ $item['title'] }}</td>
                                                                                    <td>₹{{ number_format($item['amount'], 2) }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    @endif

                                                                    @if(count($nextPayments) > 0)
                                                                    <h6 class="fw-bold text-primary mt-3">Next Payments</h6>
                                                                    <table class="table table-bordered table-striped">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Date</th>
                                                                                <th>Amount (₹)</th>
                                                                                <th>Purpose</th>
                                                                                <th>Mode</th>
                                                                                <th>Account</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($nextPayments as $np)
                                                                                @php
                                                                                    $accountName = DB::table('payment_categories')->where('id', $np['hospital_account'] ?? 0)->value('payment_category');
                                                                                @endphp
                                                                                <tr>
                                                                                    <td>{{ $np['date'] }}</td>
                                                                                    <td>₹{{ number_format($np['amount'], 2) }}</td>
                                                                                    <td>{{ $np['purpose'] ?? '-' }}</td>
                                                                                    <td>{{ $np['payment_mode'] ?? '-' }}</td>
                                                                                    <td>{{ $accountName ?? '-' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Action -->
                                                <td>
                                                    @if($pending <= 0)
                                                        <button class="btn btn-primary btn-sm" onclick="window.open('billing/print/{{ $bill->id }}', '_blank')">Print</button>
                                                    @else
                                                        <button class="btn btn-success btn-sm" onclick="showNextPaymentModal({{ $bill->id }})">Add Payment</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Next Payment Modal -->
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
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Amount (₹)</label>
                        <input type="number" name="next_payment_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="next_payment_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Purpose</label>
                        <input type="text" name="purpose" class="form-control" placeholder="Purpose of this payment">
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showNextPaymentModal(billId){
    $('#modal_bill_id').val(billId);
    $('#nextPaymentModal').modal('show');
}

 $('#billing').DataTable({
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-sm-6'i><'col-sm-6'p>>",
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
$('#nextPaymentForm').submit(function(e){
    e.preventDefault();
    let billId = $('#modal_bill_id').val();
    $.ajax({
        url: 'billing/add-next-payment/' + billId,
        method: 'POST',
        data: $(this).serialize(),
        success: function(res){
            Swal.fire({ icon: 'success', title: 'Success', text: res.message })
            .then(()=> location.reload());
        },
        error: function(xhr){
            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText });
        }
    });
});
</script>


@endsection
