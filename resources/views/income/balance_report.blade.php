@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-4">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <div>
                        <h3 class="fw-bold text-primary mb-1">
                            <i class="fa-solid fa-scale-balanced me-2"></i>Income, Expenses &amp; Daily Closing Balance Report
                        </h3>
                        <p class="text-muted mb-0 small">Daily collection breakdown and cumulative running balances for Cash and GPay</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('income.create') }}" class="btn btn-outline-success">
                            <i class="fa-solid fa-plus me-1"></i> Create Income
                        </a>
                        <a href="{{ route('new-expenses.create') }}" class="btn btn-outline-danger">
                            <i class="fa-solid fa-plus me-1"></i> Create Expense
                        </a>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('income.balance-report') }}" class="row g-3 align-items-end">
                            <div class="col-md-3 col-sm-6">
                                <label for="from_date" class="form-label fw-semibold text-secondary small">
                                    <i class="fa-regular fa-calendar me-1"></i> From Date
                                </label>
                                <input type="date" id="from_date" name="from_date"
                                       class="form-control @error('from_date') is-invalid @enderror"
                                       value="{{ request('from_date', $fromDate) }}">
                                @error('from_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label for="to_date" class="form-label fw-semibold text-secondary small">
                                    <i class="fa-regular fa-calendar me-1"></i> To Date
                                </label>
                                <input type="date" id="to_date" name="to_date"
                                       class="form-control @error('to_date') is-invalid @enderror"
                                       value="{{ request('to_date', $toDate) }}">
                                @error('to_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label for="payment_mode" class="form-label fw-semibold text-secondary small">
                                    <i class="fa-solid fa-filter me-1"></i> Payment Mode
                                </label>
                                <select name="payment_mode" id="payment_mode" class="form-select">
                                    <option value="All" {{ in_array(request('payment_mode', $selectedPaymentMode), ['All', 'all', 'both'], true) ? 'selected' : '' }}>All (Cash &amp; GPay)</option>
                                    <option value="Cash" {{ request('payment_mode', $selectedPaymentMode) === 'Cash' ? 'selected' : '' }}>Cash Only</option>
                                    <option value="GPay" {{ request('payment_mode', $selectedPaymentMode) === 'GPay' ? 'selected' : '' }}>GPay Only</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-6 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Search / Filter
                                </button>
                                <a href="{{ route('income.balance-report') }}" class="btn btn-outline-secondary w-100 fw-semibold">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Section 3: Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Overall Performance -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted text-uppercase fw-bold small">Total Income</span>
                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                                        <i class="fa-solid fa-arrow-down-long fs-6"></i>
                                    </span>
                                </div>
                                <h3 class="fw-bold text-success mb-1">₹{{ number_format($totalIncome, 2) }}</h3>
                                <div class="text-muted small">
                                    @if($selectedPaymentMode === 'Cash')
                                        Cash collections only
                                    @elseif($selectedPaymentMode === 'GPay')
                                        GPay collections only
                                    @else
                                        Cash: ₹{{ number_format($cashIncome, 2) }} | GPay: ₹{{ number_format($gpayIncome, 2) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted text-uppercase fw-bold small">Total Expenses</span>
                                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle">
                                        <i class="fa-solid fa-arrow-up-long fs-6"></i>
                                    </span>
                                </div>
                                <h3 class="fw-bold text-danger mb-1">₹{{ number_format($totalExpenses, 2) }}</h3>
                                <div class="text-muted small">
                                    @if($selectedPaymentMode === 'Cash')
                                        Cash expenses only
                                    @elseif($selectedPaymentMode === 'GPay')
                                        GPay expenses only
                                    @else
                                        Cash: ₹{{ number_format($cashExpenses, 2) }} | GPay: ₹{{ number_format($gpayExpenses, 2) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="card shadow-sm border-0 rounded-4 h-100 {{ $netBalance >= 0 ? 'bg-primary-subtle border-primary' : 'bg-danger-subtle border-danger' }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-uppercase fw-bold small {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">Net Balance</span>
                                    <span class="badge {{ $netBalance >= 0 ? 'bg-primary text-white' : 'bg-danger text-white' }} p-2 rounded-circle">
                                        <i class="fa-solid fa-scale-balanced fs-6"></i>
                                    </span>
                                </div>
                                <h3 class="fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mb-1">
                                    ₹{{ number_format($netBalance, 2) }}
                                </h3>
                                <div class="small {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                                    Total Income − Total Expenses
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Breakdown -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white border-start border-4 border-primary">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-money-bill-wave text-primary me-2"></i>
                                    <span class="text-muted fw-bold small text-uppercase">Cash Income</span>
                                </div>
                                <h4 class="fw-bold text-success mb-0">₹{{ number_format($cashIncome, 2) }}</h4>
                                <span class="text-muted small">Includes patient cash &amp; hand cash</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white border-start border-4 border-danger">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-receipt text-danger me-2"></i>
                                    <span class="text-muted fw-bold small text-uppercase">Cash Expenses</span>
                                </div>
                                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($cashExpenses, 2) }}</h4>
                                <span class="text-muted small">Direct cash payouts</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white border-start border-4 border-info">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-wallet text-info me-2"></i>
                                    <span class="text-muted fw-bold small text-uppercase">Cash Closing Balance</span>
                                </div>
                                <h4 class="fw-bold {{ $cashClosingBalance >= 0 ? 'text-info' : 'text-danger' }} mb-0">
                                    ₹{{ number_format($cashClosingBalance, 2) }}
                                </h4>
                                <span class="text-muted small">Cash Income − Cash Expenses</span>
                            </div>
                        </div>
                    </div>

                    <!-- GPay Breakdown -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white border-start border-4 border-primary">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-mobile-screen-button text-primary me-2"></i>
                                    <span class="text-muted fw-bold small text-uppercase">GPay Income</span>
                                </div>
                                <h4 class="fw-bold text-success mb-0">₹{{ number_format($gpayIncome, 2) }}</h4>
                                <span class="text-muted small">Digital UPI / GPay collections</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white border-start border-4 border-danger">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-file-invoice text-danger me-2"></i>
                                    <span class="text-muted fw-bold small text-uppercase">GPay Expenses</span>
                                </div>
                                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($gpayExpenses, 2) }}</h4>
                                <span class="text-muted small">Digital payouts via GPay</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100 bg-white border-start border-4 border-info">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-building-columns text-info me-2"></i>
                                    <span class="text-muted fw-bold small text-uppercase">GPay Closing Balance</span>
                                </div>
                                <h4 class="fw-bold {{ $gpayClosingBalance >= 0 ? 'text-info' : 'text-danger' }} mb-0">
                                    ₹{{ number_format($gpayClosingBalance, 2) }}
                                </h4>
                                <span class="text-muted small">GPay Income − GPay Expenses</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Opening Balance & Carry-Forward Summary Strip -->
                <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #eef2f7 100%);">
                    <div class="card-body p-3">
                        <div class="row align-items-center text-center text-md-start g-3">
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-white">
                                    <span class="text-muted small d-block fw-semibold text-uppercase">
                                        <i class="fa-solid fa-hourglass-start me-1 text-secondary"></i> Opening Balance
                                        @if($fromDate)
                                            <span class="fw-normal text-muted">(Before {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }})</span>
                                        @else
                                            <span class="fw-normal text-muted">(All Time)</span>
                                        @endif
                                    </span>
                                    <h4 class="fw-bold text-secondary mb-1">₹{{ number_format($openingTotal, 2) }}</h4>
                                    <div class="small text-muted">
                                        Cash: <span class="fw-semibold">₹{{ number_format($openingCash, 2) }}</span> |
                                        GPay: <span class="fw-semibold">₹{{ number_format($openingGPay, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-white">
                                    <span class="text-muted small d-block fw-semibold text-uppercase">
                                        <i class="fa-solid fa-right-left me-1 text-primary"></i> Period Net Change
                                    </span>
                                    <h4 class="fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mb-1">
                                        ₹{{ number_format($netBalance, 2) }}
                                    </h4>
                                    <div class="small text-muted">
                                        Cash Net: <span class="fw-semibold">₹{{ number_format($cashClosingBalance, 2) }}</span> |
                                        GPay Net: <span class="fw-semibold">₹{{ number_format($gpayClosingBalance, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-white">
                                    <span class="text-muted small d-block fw-semibold text-uppercase">
                                        <i class="fa-solid fa-flag-checkered me-1 text-success"></i> Cumulative Closing Balance
                                        @if($toDate)
                                            <span class="fw-normal text-muted">(As of {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }})</span>
                                        @endif
                                    </span>
                                    <h4 class="fw-bold {{ $finalClosingTotal >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                                        ₹{{ number_format($finalClosingTotal, 2) }}
                                    </h4>
                                    <div class="small text-muted">
                                        Cash: <span class="fw-semibold">₹{{ number_format($finalClosingCash, 2) }}</span> |
                                        GPay: <span class="fw-semibold">₹{{ number_format($finalClosingGPay, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Breakdown Section -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-primary mb-1">
                                <i class="fa-solid fa-calendar-days me-2"></i>Daily Breakdown &amp; Closing Balance
                            </h5>
                            <p class="text-muted small mb-0">Transactions grouped by date (sorted newest first)</p>
                        </div>

                        <!-- View Toggle (Section 4 Daily vs Section 6 Running) -->
                        <div class="btn-group shadow-sm" role="group" aria-label="Table View Mode">
                            <button type="button" class="btn btn-sm btn-primary active fw-semibold" id="btnDailyView" onclick="switchTableView('daily')">
                                <i class="fa-solid fa-table me-1"></i> Daily Transaction View
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btnRunningView" onclick="switchTableView('running')">
                                <i class="fa-solid fa-chart-line me-1"></i> Running Balance View
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Table 1: Section 4 Daily Transaction Balance Table -->
                        <div class="table-responsive" id="dailyViewContainer">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th rowspan="2" class="text-start" style="min-width: 110px;">Date</th>
                                        <th colspan="3" class="table-primary border-bottom-0">Cash (₹)</th>
                                        <th colspan="3" class="table-info border-bottom-0">GPay (₹)</th>
                                        <th colspan="3" class="table-secondary border-bottom-0">Daily Total (₹)</th>
                                    </tr>
                                    <tr class="small text-muted">
                                        <!-- Cash -->
                                        <th class="table-primary">Cash Income</th>
                                        <th class="table-primary">Cash Expenses</th>
                                        <th class="table-primary fw-bold text-dark">Cash Closing</th>
                                        <!-- GPay -->
                                        <th class="table-info">GPay Income</th>
                                        <th class="table-info">GPay Expenses</th>
                                        <th class="table-info fw-bold text-dark">GPay Closing</th>
                                        <!-- Total -->
                                        <th class="table-secondary">Total Income</th>
                                        <th class="table-secondary">Total Expenses</th>
                                        <th class="table-secondary fw-bold text-dark">Daily Net Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                        <tr class="text-end">
                                            <td class="text-start fw-bold text-dark">
                                                <i class="fa-regular fa-calendar-check text-primary me-1"></i>
                                                {{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}
                                            </td>

                                            <!-- Cash -->
                                            <td class="text-success fw-semibold">₹{{ number_format($row->cash_income, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($row->cash_expenses, 2) }}</td>
                                            <td class="fw-bold {{ $row->cash_closing >= 0 ? 'text-primary' : 'text-danger' }}">
                                                ₹{{ number_format($row->cash_closing, 2) }}
                                            </td>

                                            <!-- GPay -->
                                            <td class="text-success fw-semibold">₹{{ number_format($row->gpay_income, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($row->gpay_expenses, 2) }}</td>
                                            <td class="fw-bold {{ $row->gpay_closing >= 0 ? 'text-primary' : 'text-danger' }}">
                                                ₹{{ number_format($row->gpay_closing, 2) }}
                                            </td>

                                            <!-- Daily Total -->
                                            <td class="text-success fw-bold">₹{{ number_format($row->total_income, 2) }}</td>
                                            <td class="text-danger fw-bold">₹{{ number_format($row->total_expenses, 2) }}</td>
                                            <td class="fw-bold {{ $row->daily_net >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                                                ₹{{ number_format($row->daily_net, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-receipt fa-2x mb-3 text-secondary d-block"></i>
                                                <h6 class="fw-semibold">No income or expense records found for the selected filters.</h6>
                                                <p class="small text-muted mb-0">Try adjusting your date range or payment mode filter.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($rows->isNotEmpty())
                                    <tfoot class="table-dark text-end fw-bold align-middle">
                                        <tr>
                                            <td class="text-start text-uppercase">Grand Total</td>
                                            <td class="text-success">₹{{ number_format($cashIncome, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($cashExpenses, 2) }}</td>
                                            <td class="{{ $cashClosingBalance >= 0 ? 'text-white' : 'text-warning' }}">
                                                ₹{{ number_format($cashClosingBalance, 2) }}
                                            </td>
                                            <td class="text-success">₹{{ number_format($gpayIncome, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($gpayExpenses, 2) }}</td>
                                            <td class="{{ $gpayClosingBalance >= 0 ? 'text-white' : 'text-warning' }}">
                                                ₹{{ number_format($gpayClosingBalance, 2) }}
                                            </td>
                                            <td class="text-success">₹{{ number_format($totalIncome, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($totalExpenses, 2) }}</td>
                                            <td class="{{ $netBalance >= 0 ? 'text-warning' : 'text-danger' }} fs-6">
                                                ₹{{ number_format($netBalance, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <!-- Table 2: Section 6 Cumulative Running Balance Table (Shown on Toggle) -->
                        <div class="table-responsive d-none" id="runningViewContainer">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th rowspan="2" class="text-start" style="min-width: 110px;">Date</th>
                                        <th colspan="4" class="table-primary border-bottom-0">Cash Balance (₹)</th>
                                        <th colspan="4" class="table-info border-bottom-0">GPay Balance (₹)</th>
                                        <th colspan="4" class="table-secondary border-bottom-0">Cumulative Total Balance (₹)</th>
                                    </tr>
                                    <tr class="small text-muted">
                                        <!-- Cash -->
                                        <th class="table-primary">Opening</th>
                                        <th class="table-primary">Income</th>
                                        <th class="table-primary">Expenses</th>
                                        <th class="table-primary fw-bold text-dark">Closing</th>
                                        <!-- GPay -->
                                        <th class="table-info">Opening</th>
                                        <th class="table-info">Income</th>
                                        <th class="table-info">Expenses</th>
                                        <th class="table-info fw-bold text-dark">Closing</th>
                                        <!-- Total -->
                                        <th class="table-secondary">Opening</th>
                                        <th class="table-secondary">Income</th>
                                        <th class="table-secondary">Expenses</th>
                                        <th class="table-secondary fw-bold text-dark">Cumulative Closing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                        <tr class="text-end">
                                            <td class="text-start fw-bold text-dark">
                                                <i class="fa-regular fa-calendar-check text-primary me-1"></i>
                                                {{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}
                                            </td>

                                            <!-- Cash Carry Forward -->
                                            <td class="text-muted">₹{{ number_format($row->cash_opening, 2) }}</td>
                                            <td class="text-success">₹{{ number_format($row->cash_income, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($row->cash_expenses, 2) }}</td>
                                            <td class="fw-bold {{ $row->cash_running_closing >= 0 ? 'text-primary' : 'text-danger' }}">
                                                ₹{{ number_format($row->cash_running_closing, 2) }}
                                            </td>

                                            <!-- GPay Carry Forward -->
                                            <td class="text-muted">₹{{ number_format($row->gpay_opening, 2) }}</td>
                                            <td class="text-success">₹{{ number_format($row->gpay_income, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($row->gpay_expenses, 2) }}</td>
                                            <td class="fw-bold {{ $row->gpay_running_closing >= 0 ? 'text-primary' : 'text-danger' }}">
                                                ₹{{ number_format($row->gpay_running_closing, 2) }}
                                            </td>

                                            <!-- Total Cumulative -->
                                            <td class="text-muted fw-semibold">₹{{ number_format($row->total_opening, 2) }}</td>
                                            <td class="text-success fw-bold">₹{{ number_format($row->total_income, 2) }}</td>
                                            <td class="text-danger fw-bold">₹{{ number_format($row->total_expenses, 2) }}</td>
                                            <td class="fw-bold {{ $row->total_running_closing >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                                                ₹{{ number_format($row->total_running_closing, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-receipt fa-2x mb-3 text-secondary d-block"></i>
                                                <h6 class="fw-semibold">No records found for the selected date range.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($rows->isNotEmpty())
                                    <tfoot class="table-dark text-end fw-bold align-middle">
                                        <tr>
                                            <td class="text-start text-uppercase">Cumulative Position</td>
                                            <td class="text-muted">₹{{ number_format($openingCash, 2) }}</td>
                                            <td class="text-success">₹{{ number_format($cashIncome, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($cashExpenses, 2) }}</td>
                                            <td class="{{ $finalClosingCash >= 0 ? 'text-white' : 'text-warning' }}">
                                                ₹{{ number_format($finalClosingCash, 2) }}
                                            </td>

                                            <td class="text-muted">₹{{ number_format($openingGPay, 2) }}</td>
                                            <td class="text-success">₹{{ number_format($gpayIncome, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($gpayExpenses, 2) }}</td>
                                            <td class="{{ $finalClosingGPay >= 0 ? 'text-white' : 'text-warning' }}">
                                                ₹{{ number_format($finalClosingGPay, 2) }}
                                            </td>

                                            <td class="text-muted">₹{{ number_format($openingTotal, 2) }}</td>
                                            <td class="text-success">₹{{ number_format($totalIncome, 2) }}</td>
                                            <td class="text-danger">₹{{ number_format($totalExpenses, 2) }}</td>
                                            <td class="{{ $finalClosingTotal >= 0 ? 'text-warning' : 'text-danger' }} fs-6">
                                                ₹{{ number_format($finalClosingTotal, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTableView(mode) {
    var dailyContainer = document.getElementById('dailyViewContainer');
    var runningContainer = document.getElementById('runningViewContainer');
    var btnDaily = document.getElementById('btnDailyView');
    var btnRunning = document.getElementById('btnRunningView');

    if (mode === 'daily') {
        dailyContainer.classList.remove('d-none');
        runningContainer.classList.add('d-none');
        btnDaily.classList.add('active', 'btn-primary');
        btnDaily.classList.remove('btn-outline-primary');
        btnRunning.classList.remove('active', 'btn-primary');
        btnRunning.classList.add('btn-outline-primary');
    } else {
        runningContainer.classList.remove('d-none');
        dailyContainer.classList.add('d-none');
        btnRunning.classList.add('active', 'btn-primary');
        btnRunning.classList.remove('btn-outline-primary');
        btnDaily.classList.remove('active', 'btn-primary');
        btnDaily.classList.add('btn-outline-primary');
    }
}
</script>
@endsection
