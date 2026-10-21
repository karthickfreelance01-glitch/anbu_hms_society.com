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

            <form method="POST" action="{{ url('discharge/update/' . $patient->id) }}">
                @csrf
               
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <input type="hidden" id="final_amount_input" value="{{ $discharge->final_amount ?? $totalPending }}">

                <div class="card shadow-lg border-0 p-5 mt-4 mb-5 rounded-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="fw-bold text-primary mb-1">EDIT DISCHARGE BILL</h2>
                            <p class="text-muted">Update Billing & Discharge Summary</p>
                        </div>
                        <div>
                            <a href="{{ route('patient.discharge.pdf', $patient->id) }}" 
                               target="_blank" 
                               class="btn btn-outline-danger">
                                <i class="fas fa-file-pdf me-2"></i>View PDF
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
                                    <td id="admission_date" data-iso="{{ $admissionDate->format('Y-m-d') }}">
                                        {{ $admissionDate->format('d-m-Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Discharge Date:</th>
                                    <td>
                                        <input type="date" 
                                               name="discharge_date" 
                                               id="discharge_date"
                                               class="form-control"
                                               value="{{ old('discharge_date', $dischargeDate->format('Y-m-d')) }}" 
                                               required>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Months Stayed:</th>
                                    <td><span id="months_stayed">{{ $monthsStayed ?? 0 }}</span> month(s)</td>
                                </tr>
                                <tr>
                                    <th>Extra Days:</th>
                                    <td><span id="extra_days">{{ $extraDays ?? 0 }}</span> day(s)</td>
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
                                    @foreach($allBillings as $bill)
                                    <tr>
                                        <td>{{ ucfirst($bill->category) }}</td>
                                        <td class="text-end total_amount">₹ {{ number_format($bill->total_amount, 2) }}</td>
                                        <td class="text-end">₹ {{ number_format($bill->total_received, 2) }}</td>
                                        <td class="text-end text-danger fw-semibold pending_amount">₹ {{ number_format($bill->total_pending, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Amount</th>
                                    <td class="text-end">₹<span id="total_amount">{{ number_format($totalAmount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Total Received</th>
                                    <td class="text-end">₹<span id="total_received">{{ number_format($totalReceived, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Total Pending</th>
                                    <td class="text-end text-danger fw-bold">₹<span id="pending_amount">{{ number_format($totalPending, 2) }}</span></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Final Amount to Pay</th>
                                    <td>
                                        <input type="number" id="final_amount_box" class="form-control text-end fw-bold fs-5"
                                               value="{{ old('final_amount', $discharge->final_amount ?? $totalPending) }}" 
                                               name="final_amount" min="0" step="0.01">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Discount (₹)</th>
                                    <td>
                                        <input type="number" name="discount" id="discount" 
                                               class="form-control text-end" 
                                               value="{{ old('discount', $discharge->discount ?? 0) }}"  
                                               min="0" step="0.01">
                                    </td>
                                </tr>
                                <tr class="table-success align-middle">
                                    <th class="fw-bold">Payment Mode</th>
                                    <td>
                                        <select name="payment_mode" id="payment_mode" class="form-select fw-bold fs-5" required>
                                            <option value="" disabled>-- Select Payment Mode --</option>
                                            @foreach(['Cash','Card','UPI','Bank Transfer','Cheque','Other'] as $mode)
                                                <option value="{{ $mode }}" 
                                                    {{ old('payment_mode', $discharge->payment_mode ?? '') === $mode ? 'selected' : '' }}>
                                                    {{ $mode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Discharge Advice -->
                  <div class="mb-3">
                        <label for="discharge_advice" class="form-label fw-bold text-primary">
                            Discharge Advice / Notes
                        </label>
                        <textarea name="discharge_advice" id="discharge_advice" rows="4"
                            class="form-control shadow-sm"
                            placeholder="Enter doctor's discharge advice...">{{ old('discharge_advice', strip_tags($patient->discharge_advice ?? '')) }}</textarea>
                    </div>

                    <!-- ✅ Optional preview -->
                    @if(!empty($patient->discharge_advice))
                    <div class="mt-3 p-3 border rounded bg-light">
                        <h6 class="text-muted fw-bold mb-2">Preview:</h6>
                        <div>{!! $patient->discharge_advice !!}</div>
                    </div>
                    @endif




                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="updateDischargeBtn">
                            <i class="fas fa-save me-2"></i> Update Discharge
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyAmount = parseFloat({{ $monthlyAmount ?? 0 }}) || 0;
    const perDayAmount = parseFloat({{ $perDayAmount ?? 0 }}) || 0;
    const receivedAmount = parseFloat({{ $receivedAmount ?? 0 }}) || 0;

    const admissionIso = document.getElementById('admission_date').dataset.iso;
    const dischargeInput = document.getElementById('discharge_date');
    const monthsEl = document.getElementById('months_stayed');
    const daysEl = document.getElementById('extra_days');
    const totalAmountEl = document.getElementById('total_amount');
    const pendingEl = document.getElementById('pending_amount');
    const discountInput = document.getElementById('discount');
    const finalBox = document.getElementById('final_amount_box');
    const finalHidden = document.getElementById('final_amount_input');
    const firstRow = document.querySelector('#billing_table tbody tr:first-child');

    let totalPending = parseFloat({{ $totalPending ?? 0 }}) || 0;

    // ✅ Helper: get days in a given month
    function daysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    }

    // ✅ Correct stay calculation (no +1 for discharge month)
    function calculateStay(admDate, disDate) {
        const adm = new Date(admDate);
        const dis = new Date(disDate);
        if (dis < adm) return { months: 0, extraDays: 0 };

        let months = 0;
        let temp = new Date(adm);
        while (true) {
            const next = new Date(temp);
            next.setMonth(next.getMonth() + 1);
            if (next <= dis) {
                months++;
                temp = next;
            } else break;
        }

        const msPerDay = 1000 * 60 * 60 * 24;
        let extraDays = Math.floor((dis - temp) / msPerDay);

        // ✅ Count 31-day months between admission and month before discharge
        let extraFrom31 = 0;
        let iter = new Date(adm);
        iter.setDate(1);
        while (iter.getFullYear() < dis.getFullYear() ||
               (iter.getFullYear() === dis.getFullYear() && iter.getMonth() < dis.getMonth())) {
            if (daysInMonth(iter.getFullYear(), iter.getMonth()) === 31) {
                extraFrom31++;
            }
            iter.setMonth(iter.getMonth() + 1);
        }

        extraDays += extraFrom31;
        return { months, extraDays };
    }

    function recalcStay() {
        const { months, extraDays } = calculateStay(admissionIso, dischargeInput.value);
        monthsEl.innerText = months;
        daysEl.innerText = extraDays;

        const stayAmount = (months * monthlyAmount) + (extraDays * perDayAmount);
        const stayPending = Math.max(stayAmount - receivedAmount, 0);

        if (firstRow) {
            firstRow.querySelector('.total_amount').textContent = '₹ ' + stayAmount.toFixed(2);
            firstRow.querySelector('.pending_amount').textContent = '₹ ' + stayPending.toFixed(2);
        }

        let totalAmt = 0, pendingAmt = 0;
        document.querySelectorAll('#billing_table tbody tr').forEach(row => {
            totalAmt += parseFloat(row.querySelector('.total_amount')?.textContent.replace(/[^\d.]/g, '')) || 0;
            pendingAmt += parseFloat(row.querySelector('.pending_amount')?.textContent.replace(/[^\d.]/g, '')) || 0;
        });

        totalAmountEl.textContent = totalAmt.toFixed(2);
        pendingEl.textContent = pendingAmt.toFixed(2);
        totalPending = pendingAmt;
        updateFinalAmount();
    }

    function updateFinalAmount() {
        const discount = parseFloat(discountInput.value) || 0;
        let final = totalPending - discount;
        if (final < 0) final = 0;
        finalBox.value = final.toFixed(2);
        if (finalHidden) finalHidden.value = final.toFixed(2);
    }

    dischargeInput.addEventListener('change', recalcStay);
    discountInput.addEventListener('input', updateFinalAmount);
    recalcStay();
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#discharge_advice')).catch(e => console.error(e));
</script>

@endsection
