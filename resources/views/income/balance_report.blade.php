@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h3 class="fw-bold text-primary mb-0">Income &amp; Expenses Balance Report</h3>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('income.balance-report') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="from_date" class="form-label fw-bold">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" value="{{ request('from_date') }}">
                                @error('from_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label fw-bold">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" value="{{ request('to_date') }}">
                                @error('to_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="payment_mode" class="form-label fw-bold">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-select">
                                    <option value="both" {{ request('payment_mode', 'both') === 'both' ? 'selected' : '' }}>Both</option>
                                    <option value="Cash" {{ request('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="GPay" {{ request('payment_mode') === 'GPay' ? 'selected' : '' }}>GPay</option>
                                    <option value="Hand Cash" {{ request('payment_mode') === 'Hand Cash' ? 'selected' : '' }}>Hand Cash</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('income.balance-report') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Payment Mode</th>
                                        <th>Income (₹)</th>
                                        <th>Expenses (₹)</th>
                                        <th>Balance (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->report_date)->format('d-m-Y') }}</td>
                                            <td>{{ $row->payment_mode }}</td>
                                            <td class="text-success">₹{{ number_format($row->total_income, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($row->total_expenses, 2) }}</td>
                                            <td class="{{ $row->balance < 0 ? 'text-danger' : 'text-primary' }}">₹{{ number_format($row->balance, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No income or expense records found for the selected filters.</td>
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
@endsection
