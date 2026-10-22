@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h3 class="fw-bold text-primary mb-0">New Expense Report</h3>
                    <a href="{{ route('new-expenses.create') }}" class="btn btn-success">Create Expense</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('new-expenses.reports') }}" class="row g-3 align-items-end">
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
                                <a href="{{ route('new-expenses.reports') }}" class="btn btn-secondary w-100">Reset</a>
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
                                        <th>Expense</th>
                                        <th>Date</th>
                                        <th>Amount (₹)</th>
                                        <th>Payment Mode</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $index => $expense)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $expense->expense_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}</td>
                                            <td>₹{{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ $expense->payment_mode }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('new-expenses.edit', $expense->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" action="{{ route('new-expenses.destroy', $expense->id) }}" class="d-inline js-confirm-delete"
                                                      data-confirm-message="Delete this expense record?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No expenses found for the selected filters.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $expenses->links() }}
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
