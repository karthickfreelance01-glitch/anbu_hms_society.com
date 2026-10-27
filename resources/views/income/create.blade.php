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
                        <h3 class="card-title mb-0 text-primary fw-bold">Create Income</h3>
                        <a href="{{ route('income.ledger.reports') }}" class="btn btn-outline-primary">Income Report</a>
                    </div>

                    <form method="POST" action="{{ route('income.store') }}" id="createIncomeForm">
                        @csrf
                        <input type="hidden" name="submission_key" value="{{ old('submission_key', $submissionKey) }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="patient_id" class="form-label">Select Patient / Collection Source <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-select" required>
                                    <option value="">-- Select Patient or Hand Cash --</option>
                                    <option value="hand_cash" {{ old('patient_id') === 'hand_cash' ? 'selected' : '' }}>Hand Cash / Cash Collection</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} ({{ $patient->ip_number ?? $patient->reg_no ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="income_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="income_date" class="form-control"
                                       value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control"
                                       min="0.01" step="0.01" value="{{ old('amount') }}" required>
                                @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="payment_mode" class="form-select" required>
                                    <option value="">-- Select Payment Mode --</option>
                                    <option value="Cash" {{ old('payment_mode') === 'Cash' || old('payment_mode') === 'Hand Cash' ? 'selected' : '' }}>Cash / Hand Cash</option>
                                    <option value="GPay" {{ old('payment_mode') === 'GPay' ? 'selected' : '' }}>GPay</option>
                                </select>
                                @error('payment_mode')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSubmitIncome">Save Income</button>
                            <a href="{{ route('income.ledger.reports') }}" class="btn btn-secondary btn-lg px-5 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('#patient_id').select2({
        placeholder: 'Search or select a patient / Hand Cash',
        allowClear: true,
        width: '100%'
    });

    $('#patient_id').on('change', function () {
        if ($(this).val() === 'hand_cash') {
            $('#payment_mode').val('Cash');
        }
    });

    $('#createIncomeForm').on('submit', function (e) {
        var form = this;
        if ($(form).data('submitted') === true) {
            e.preventDefault();
            return false;
        }

        $(form).data('submitted', true);
        var $btn = $('#btnSubmitIncome');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');
    });
});
</script>
@endsection
