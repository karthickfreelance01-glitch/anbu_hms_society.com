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

            <form method="POST" action="{{ route('patient.discharge.confirm') }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <input type="hidden" name="final_amount" id="final_amount_input" value="{{ $totalPending }}">

                <div class="card shadow-lg border-0 p-5 mt-4 mb-5 rounded-4">

                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary mb-1">PATIENT DISCHARGE BILL</h2>
                        <p class="text-muted">Final Billing & Discharge Summary</p>
                        <hr>
                    </div>

                    <!-- Patient Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th>Patient Name:</th><td>{{ $patient->name }}</td></tr>
                                <tr><th>IP Number:</th><td>{{ $patient->ip_number }}</td></tr>
                                <tr><th>Gender:</th><td>{{ $patient->gender }}</td></tr>
                                <tr><th>Contact No:</th><td>{{ $patient->contact_number }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th>Admission Date:</th><td>{{ $admissionDate->format('d-m-Y') }}</td></tr>
                                <tr>
                                    <th>Discharge Date:</th>
                                    <td>
                                        <input type="date" name="discharge_date" id="discharge_date" class="form-control"
                                            value="{{ $dischargeDate->format('Y-m-d') }}" required>
                                    </td>
                                </tr>
                                <tr><th>Months Stayed:</th><td><span id="months_stayed">{{ $monthsStayed }}</span> month(s)</td></tr>
                                <tr><th>Extra Days:</th><td><span id="extra_days">{{ $extraDays }}</span> day(s)</td></tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Billing Table -->
                    <div class="mt-4">
                        <h5 class="fw-bold text-primary mb-3">CATEGORY-WISE BILL DETAILS</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Total (₹)</th>
                                        <th class="text-end">Received (₹)</th>
                                        <th class="text-end">Pending (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allBillings as $bill)
                                    <tr>
                                        <td>{{ ucfirst($bill->category) }}</td>
                                        <td class="text-end">{{ number_format($bill->total_amount, 2) }}</td>
                                        <td class="text-end">{{ number_format($bill->total_received, 2) }}</td>
                                        <td class="text-end text-danger fw-semibold">{{ number_format($bill->total_pending, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="row mt-4">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Amount</th>
                                    <td class="text-end">₹{{ number_format($totalAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Received</th>
                                    <td class="text-end">₹{{ number_format($totalReceived, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Pending</th>
                                    <td class="text-end text-danger fw-bold">
                                        ₹<span id="pending_amount">{{ number_format($totalPending, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Discount (₹)</th>
                                    <td>
                                        <input type="number" name="discount" id="discount" class="form-control text-end" value="0" min="0">
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <th>Final Amount to Pay</th>
                                    <td class="text-end fw-bold fs-5">
                                        ₹<span id="final_amount">{{ number_format($totalPending, 2) }}</span>
                                    </td>
                                </tr>
                                <tr class="table-success align-middle">
                                    <th class="fw-bold">Payment Mode</th>
                                    <td class="text-end">
                                        <select name="payment_mode" id="payment_mode" class="form-select fw-bold fs-5" required>
                                            <option value="" selected disabled>-- Select Payment Mode --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Card">Card</option>
                                            <option value="UPI">UPI</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- ✅ Discharge Advice Section -->
                    <div class="mt-4">
                        <label for="discharge_advice" class="form-label fw-bold text-primary">Discharge Advice / Notes</label>
                        <textarea name="discharge_advice" id="discharge_advice" rows="4" class="form-control shadow-sm"
                            placeholder="Enter doctor's discharge advice, follow-up instructions, or notes..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="confirmDischargeBtn">
                            <i class="fas fa-check-circle me-2"></i> Confirm Discharge
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Section -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const pendingAmount = {{ $totalPending }};
    const discountInput = document.getElementById('discount');
    const finalAmountSpan = document.getElementById('final_amount');
    const finalAmountHidden = document.getElementById('final_amount_input');

    // ✅ Calculate Final Amount after discount
    discountInput.addEventListener('input', () => {
        let discount = parseFloat(discountInput.value) || 0;
        let finalAmount = pendingAmount - discount;
        if (finalAmount < 0) finalAmount = 0;
        finalAmountSpan.innerText = finalAmount.toFixed(2);
        finalAmountHidden.value = finalAmount.toFixed(2);
    });

    // ✅ Show confirmation before submitting
    document.getElementById('confirmDischargeBtn').addEventListener('click', (e) => {
        e.preventDefault();
        Swal.fire({
            title: 'Confirm Discharge?',
            text: 'This will finalize discharge and update billing records.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Confirm',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.closest('form').submit();
            }
        });
    });
</script>
@endsection
