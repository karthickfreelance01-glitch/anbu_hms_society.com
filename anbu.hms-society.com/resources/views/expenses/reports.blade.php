@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold text-primary">Expense Reports</h3>
                </div>

                <!-- Filter Form -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('expenses.reports') }}" class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            <!-- Expense Category -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Expense Category</label>
                                <select id="sub_category" name="sub_category_id" class="form-select select2">
                                    <option value="">-- All Categories --</option>
                                    @foreach($subcategories as $id => $name)
                                        <option value="{{ $id }}" {{ request('sub_category_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Account Type -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Account Type</label>
                                <select name="accounttype" class="form-select">
                                    <option value="">-- All Account Types --</option>
                                    <option value="Cash" {{ request('accounttype') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Savings" {{ request('accounttype') == 'Savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="Current account" {{ request('accounttype') == 'Current account' ? 'selected' : '' }}>Current Account</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('expenses.reports') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Overall Total -->
                <div class="card shadow-sm rounded-4 mt-3">
                    <div class="card-body text-end">
                        <h5 class="fw-bold text-success">Overall Total Expenses: ₹ {{ number_format($totalAmount, 2) }}</h5>
                    </div>
                </div>

                <!-- Expenses Grouped by Expense Name -->
                @forelse($groupedExpenses as $expenseName => $expensesList)
                    <div class="card shadow-lg rounded-4 mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">{{ $expenseName }}</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Notes</th>
                                        <th>Amount (₹)</th>
                                        <th>Date</th>
                                        <th>Account Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expensesList as $key => $exp)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $exp->notes ?? '-' }}</td>
                                            <td>₹ {{ number_format($exp->amount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d-m-Y') }}</td>
                                            <td>{{ $exp->account_type ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total for {{ $expenseName }}</th>
                                        <th colspan="3" class="text-success fw-bold">
                                            ₹ {{ number_format($expensesList->sum('amount'), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <h6>No expenses found for the selected filters</h6>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){
    $('.select2').select2({
        width: '100%',
        allowClear: true,
        placeholder: "Choose Expense Category"
    });
});
</script>

<style>
.table th { background: #f8f9fa; font-weight: 600; }
.table td { vertical-align: middle; }
</style>

@endsection
