@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold text-primary">Hospital Account Report</h3>
                </div>

                <!-- Filter Form -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('accounttype.reports') }}" class="row g-3 align-items-end">
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
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ optional($from_date)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ optional($to_date)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('accounttype.reports') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center"><div class="card-body"><h6>Medicine Income</h6><h4 class="text-success">₹ {{ number_format($medicine_paid, 2) }}</h4></div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center"><div class="card-body"><h6>Patient Income</h6><h4 class="text-success">₹ {{ number_format($billing_paid, 2) }}</h4></div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center"><div class="card-body"><h6>Loan Income</h6><h4 class="text-success">₹ {{ number_format($loan_amount_total, 2) }}</h4></div></div>
                    </div>
                    
                </div>

                <!-- Overall -->
                <div class="row g-3 mb-4 justify-content-center">
                    <div class="col-md-4"><div class="card shadow-sm text-center"><div class="card-body"><h6>Total Income</h6><h4 class="text-success">₹ {{ number_format($overallIncome, 2) }}</h4></div></div></div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center"><div class="card-body"><h6>Total Expenses</h6><h4 class="text-danger">₹ {{ number_format($overallExpenses, 2) }}</h4></div></div>
                    </div>

                <!-- Tables -->
               <!-- Medicine Bills Table -->
                @if($medicineBills->count())
                <div class="card shadow mt-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Medicine Bills</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Initial Payment</th>
                                    <th>Final Amount</th>
                                    <th>Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicineBills as $key => $bill)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $bill->patient_name ?? '-' }}</td>
                                    <td>₹ {{ number_format($bill->initial_payment, 2) }}</td>
                                    <td>₹ {{ number_format($bill->final_amount, 2) }}</td>
                                    <td>₹ {{ number_format($bill->final_amount - $bill->initial_payment, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Patient Bills Table -->
                @if($billing->count())
                <div class="card shadow mt-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Patient Bills</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Total Amount</th>
                                    <th>Paid</th>
                                    <th>Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billing as $key => $bill)
                                @php
                                    $nextPayments = json_decode($bill->next_payments, true) ?? [];
                                    $nextPaid = array_sum(array_map(fn($p) => floatval($p['amount'] ?? 0), $nextPayments));
                                    $totalPaid = ($bill->initial_payment ?? 0) + $nextPaid;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $bill->name ?? '-' }}</td>
                                    <td>₹ {{ number_format($bill->total_amount, 2) }}</td>
                                    <td>₹ {{ number_format($totalPaid, 2) }}</td>
                                    <td>₹ {{ number_format($bill->total_amount - $totalPaid, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Loans Table -->
                @if($loans->count())
                <div class="card shadow mt-4">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">Loan Income</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Loan By</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $key => $loan)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $loan->bank_name}}</td>
                                    <td>₹ {{ number_format($loan->loan_amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d-m-Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Expenses Table -->
                @if($expenses->count())
                <div class="card shadow mt-4">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">Expense Details</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Main Expense Category</th>
                                    <th>Sub Expense Category</th>
                                    <th>Amount (₹)</th>
                                    <th>Date</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $key => $exp)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $exp->main_category }}</td>
                                    <td>{{ $exp->expense_name }}</td>
                                    <td>₹ {{ number_format($exp->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d-m-Y') }}</td>
                                    <td>{{ $exp->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({ 
        width: '100%', 
        allowClear: true, 
        placeholder: function() { return $(this).data('placeholder'); } 
    });

    // Remove query params from URL on page reload
    if (performance.navigation.type === 1) { // page reload
        // Reset form
        $('form').trigger('reset');
        $('.select2').val(null).trigger('change');

        // Replace URL without query parameters
        const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, url);
    }
});
</script>

<style>
.card h4 { margin:0;font-weight:600; } .card h6 { margin-bottom:10px; } .table th { background:#f8f9fa;font-weight:600; } .table td { vertical-align:middle; }
</style>
@endsection
