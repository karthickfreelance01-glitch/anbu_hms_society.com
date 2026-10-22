@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h3 class="fw-bold text-primary mb-0">Income Report</h3>
                    <a href="{{ route('income.create') }}" class="btn btn-success">Create Income</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('income.ledger.reports') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="from_date" class="form-label fw-bold">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label fw-bold">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="payment_mode" class="form-label fw-bold">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-select">
                                    <option value="both" {{ request('payment_mode', 'both') === 'both' ? 'selected' : '' }}>Both</option>
                                    <option value="Cash" {{ request('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="GPay" {{ request('payment_mode') === 'GPay' ? 'selected' : '' }}>GPay</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('income.ledger.reports') }}" class="btn btn-secondary w-100">Reset</a>
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
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Amount (₹)</th>
                                        <th>Payment Mode</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($incomes as $index => $income)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($income->patient_id === null)
                                                    Hand Cash
                                                @else
                                                    {{ $income->patient_name }} ({{ $income->ip_number ?? '-' }})
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($income->income_date)->format('d-m-Y') }}</td>
                                            <td>₹{{ number_format($income->amount, 2) }}</td>
                                            <td>{{ $income->payment_mode }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('income.edit', $income->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" action="{{ route('income.destroy', $income->id) }}" class="d-inline js-confirm-delete"
                                                      data-confirm-message="Delete this income record?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No income records found for the selected filters.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $incomes->links() }}
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
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-confirm-delete').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: form.dataset.confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
