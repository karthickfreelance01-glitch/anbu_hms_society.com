@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg rounded-4 p-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <h3 class="card-title mb-0 text-primary fw-bold">Edit New Expense</h3>
                        <a href="{{ route('new-expenses.reports') }}" class="btn btn-outline-primary">Back to Report</a>
                    </div>

                    <form method="POST" action="{{ route('new-expenses.update', $expense->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="expense_id" class="form-label">Expense Category <span class="text-danger">*</span></label>
                                <select name="expense_id" id="expense_id" class="form-select" required>
                                    <option value="">-- Select Expense --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_id', $expense->expense_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->expense_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="expense_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="expense_date" class="form-control"
                                       value="{{ old('date', $expense->expense_date) }}" required>
                                @error('date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control"
                                       min="0.01" step="0.01" value="{{ old('amount', $expense->amount) }}" required>
                                @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="payment_mode" class="form-select" required>
                                    <option value="GPay" {{ old('payment_mode', $expense->payment_mode) === 'GPay' ? 'selected' : '' }}>GPay</option>
                                    <option value="Cash" {{ old('payment_mode', $expense->payment_mode) === 'Cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                @error('payment_mode')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5">Update Expense</button>
                            <a href="{{ route('new-expenses.reports') }}" class="btn btn-secondary btn-lg px-5 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
