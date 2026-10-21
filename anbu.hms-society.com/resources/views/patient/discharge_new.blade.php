@php use Carbon\Carbon; @endphp
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="fw-bold text-primary mb-1">PATIENT DISCHARGE BILL</h2>
                            <p class="text-muted">Final Billing & Discharge Summary</p>
                        </div>
                        <div>
                            <a href="{{ route('patient.discharge.pdf', $patient->id) }}" 
                               target="_blank" 
                               class="btn btn-outline-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </a>
                        </div>
                    </div>
                    <hr>

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
                                <tr>
                                    <th>Admission Date:</th>
                                    <td><input type="date" id="admission_date" class="form-control"
                                        value="{{ $admissionDate->format('Y-m-d') }}" readonly></td>
                                </tr>
                                <tr>
                                    <th>Discharge Date:</th>
                                    <td><input type="date" name="discharge_date" id="discharge_date" class="form-control"
                                        value="{{ $dischargeDate->format('Y-m-d') }}" required></td>
                                </tr>
                                <tr>
                                    <th>Months Stayed:</th>
                                    <td>
                                        <span id="months_stayed">{{ $monthsStayed }}</span> month(s)
                                        <small class="text-muted ms-2">(Monthly Rate: ₹{{ number_format($bill->monthly_amount ?? 0, 2) }})</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Extra Days:</th>
                                    <td>
                                        <span id="extra_days">{{ $extraDays }}</span> day(s)
                                        <small class="text-muted ms-2">(Per Day Rate: ₹{{ number_format($bill->per_day ?? 0, 2) }})</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Billing Table -->
                    <div class="mt-4">
                        <h5 class="fw-bold text-primary mb-3">CATEGORY-WISE BILL DETAILS</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="billing_table">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Total (₹)</th>
                                        <th class="text-end">Received (₹)</th>
                                        <th class="text-end">Pending (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allBillings as $b)
                                    <tr>
                                        <td>{{ ucfirst($b->category) }}</td>
                                        <td class="text-end total_amount">{{ number_format($b->total_amount, 2) }}</td>
                                        <td class="text-end">{{ number_format($b->total_received, 2) }}</td>
                                        <td class="text-end text-danger fw-semibold pending_amount">{{ number_format($b->total_pending, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-bordered align-middle">
                                <tr>
                                    <th>Total Amount</th>
                                    <td class="text-end">₹<span id="total_amount">{{ number_format($totalAmount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Total Received</th>
                                    <td class="text-end">₹<span id="total_received">{{ number_format($totalReceived, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Provision (₹)</th>
                                    <td class="text-end">
                                        <input type="number" id="provision" name="provision" class="form-control text-end" value="0">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Discount (₹)</th>
                                    <td class="text-end">
                                        <input type="number" id="discount" name="discount" class="form-control text-end" value="{{ $discount ?? 0 }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Pending</th>
                                    <td class="text-end text-danger fw-bold">₹<span id="pending_amount">{{ number_format($totalPending, 2) }}</span></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Final Amount to Pay</th>
                                    <td class="text-end fw-bold fs-5">₹<span id="final_amount">{{ number_format($totalPending, 2) }}</span></td>
                                </tr>
                                <tr class="table-success align-middle">
                                    <th>Payment Mode</th>
                                    <td>
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

                    <!-- Notes -->
                    <div class="mt-4">
                        <label class="form-label fw-bold text-primary">Discharge Advice / Notes</label>
                        <textarea name="discharge_advice" id="discharge_advice" rows="6" class="form-control shadow-sm"
                            placeholder="Enter discharge advice or follow-up notes..."></textarea>
                    </div>

                    <!-- Buttons -->
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

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyAmount = {{ $bill->monthly_amount ?? 0 }};
    const perDayAmount = {{ $bill->per_day ?? 0 }};
    const receivedAmount = {{ $bill->received_amount ?? 0 }};
    const discountInput = document.getElementById('discount');
    const provisionInput = document.getElementById('provision');
    const pendingEl = document.getElementById('pending_amount');
    const totalAmountEl = document.getElementById('total_amount');
    const finalAmountSpan = document.getElementById('final_amount');
    const finalAmountHidden = document.getElementById('final_amount_input');
    const dischargeInput = document.getElementById('discharge_date');
    const admissionInput = document.getElementById('admission_date');

    function recalcTotals(dischargeDateValue) {
        const admissionDate = new Date(admissionInput.value);
        const dischargeDate = new Date(dischargeDateValue);
        if (isNaN(dischargeDate) || dischargeDate < admissionDate) return;

        const diffDays = Math.floor((dischargeDate - admissionDate) / (1000 * 60 * 60 * 24));
        const monthsStayed = Math.floor(diffDays / 30);
        const extraDays = diffDays % 30;

        document.getElementById('months_stayed').innerText = monthsStayed;
        document.getElementById('extra_days').innerText = extraDays;

        const totalStay = (monthsStayed * monthlyAmount) + (extraDays * perDayAmount);
        const pendingStay = Math.max(totalStay - receivedAmount, 0);

        const stayRow = document.querySelector('#billing_table tbody tr:first-child');
        if (stayRow) {
            stayRow.querySelector('.total_amount').innerText = totalStay.toFixed(2);
            stayRow.querySelector('.pending_amount').innerText = pendingStay.toFixed(2);
        }

        let totalAmount = 0;
        let totalPending = 0;
        document.querySelectorAll('#billing_table tbody tr').forEach(row => {
            totalAmount += parseFloat(row.querySelector('.total_amount')?.innerText || 0);
            totalPending += parseFloat(row.querySelector('.pending_amount')?.innerText || 0);
        });

        totalAmountEl.innerText = totalAmount.toFixed(2);
        pendingEl.innerText = totalPending.toFixed(2);
        updateFinalAmount();
    }

    function updateFinalAmount() {
        const totalPending = parseFloat(pendingEl.innerText) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const provision = parseFloat(provisionInput.value) || 0;

        const finalAmount = Math.max(totalPending - discount + provision, 0);
        finalAmountSpan.innerText = finalAmount.toFixed(2);
        finalAmountHidden.value = finalAmount.toFixed(2);
    }

    discountInput.addEventListener('input', updateFinalAmount);
    provisionInput.addEventListener('input', updateFinalAmount);
    dischargeInput.addEventListener('change', () => recalcTotals(dischargeInput.value));

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
            if (result.isConfirmed) e.target.closest('form').submit();
        });
    });

    recalcTotals(dischargeInput.value);
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#discharge_advice'));
</script>

@endsection
