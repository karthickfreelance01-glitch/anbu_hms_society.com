@php use Carbon\Carbon; @endphp

@extends('layout.admin')

@section('content')
<div class="main-wrapper">
@include('include.header')
@include('include.sidebar')

<div class="page-wrapper">
<div class="content pb-0">

<form id="dischargeForm" method="POST" action="{{ route('patient.discharge.confirm') }}">
@csrf
<input type="hidden" name="patient_id" value="{{ $patient->id }}">
<input type="hidden" id="final_amount_input" name="final_amount" value="{{ $totalPending }}">

<div class="card shadow-lg border-0 p-5 mt-4 mb-5 rounded-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold text-primary mb-1">PATIENT DISCHARGE BILL</h2>
            <p class="text-muted">Final Billing & Discharge Summary</p>
        </div>
        <div>
            <a href="{{ route('patient.discharge.pdf', $patient->id) }}" target="_blank"
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
                <tr><th>Contact:</th><td>{{ $patient->contact_number }}</td></tr>
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
                        <input type="date" name="discharge_date" id="discharge_date"
                               value="{{ $dischargeDate->format('Y-m-d') }}" class="form-control" required>
                    </td>
                </tr>
                <tr><th>Months Stayed:</th><td><span id="months_stayed">{{ $monthsStayed }}</span> month(s)</td></tr>
                <tr><th>Extra Days:</th><td><span id="extra_days">{{ $extraDays }}</span> day(s)</td></tr>
            </table>
        </div>
    </div>

    <!-- Stay Details -->
    <h5 class="fw-bold text-primary mt-3 mb-2">STAY (MONTHLY) DETAILS</h5>
    <table class="table table-bordered align-middle" id="stay_table">
        <thead class="table-primary text-center">
            <tr><th>S.No</th><th>Category</th><th>Period</th><th class="text-end">Total (₹)</th></tr>
        </thead>
        <tbody>
            @foreach($filledBills as $i => $b)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ $b['category'] }}</td>
                <td>{{ $b['period'] }}</td>
                <td class="text-end">₹ {{ number_format($b['total_amount'],2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Category-wise -->
    <h5 class="fw-bold text-primary mt-4 mb-2">CATEGORY-WISE BILL SUMMARY</h5>
    <table class="table table-bordered align-middle" id="summary_table">
        <thead class="table-secondary text-center">
            <tr>
                <th>Category</th>
                <th class="text-end">Total (₹)</th>
                <th class="text-end">Received (₹)</th>
                <th class="text-end">Pending (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Charges</td>
                <td class="text-end">{{ number_format($totalAmount,2) }}</td>
                <td class="text-end">{{ number_format($totalReceived,2) }}</td>
                <td class="text-end text-danger fw-semibold">{{ number_format($totalPending,2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="row mt-4">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr><th>Total Amount</th><td class="text-end">₹ <span id="total_amount">{{ number_format($totalAmount,2) }}</span></td></tr>
                <tr><th>Total Received</th><td class="text-end">₹ <span id="total_received">{{ number_format($totalReceived,2) }}</span></td></tr>
                <tr><th>Total Pending</th><td class="text-end text-danger fw-bold">₹ <span id="pending_amount">{{ number_format($totalPending,2) }}</span></td></tr>

                <tr class="table-success">
                    <th>Discount (₹)</th>
                    <td><input type="number" id="discount" class="form-control text-end" value="0"></td>
                </tr>

                <tr class="table-success">
                    <th>Final Amount</th>
                    <td><input type="number" id="final_amount_box" class="form-control text-end fw-bold fs-5" value="{{ $totalPending }}" readonly></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Notes -->
    <div class="mt-4">
        <label class="fw-bold text-primary">Discharge Advice / Notes</label>
        <textarea name="discharge_advice" id="discharge_advice" class="form-control" rows="4"></textarea>
    </div>

    <div class="text-center mt-4">
        <button type="button" id="confirmBtn" class="btn btn-success btn-lg px-5">
            <i class="fas fa-check-circle me-2"></i>Confirm Discharge
        </button>
    </div>

</div>
</form>

</div>
</div>
</div>


<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const discharge = document.getElementById("discharge_date");
    const summaryTable = document.querySelector("#summary_table tbody");
    const stayTable = document.querySelector("#stay_table tbody");

    discharge.addEventListener("change", recalc);

    function recalc() {
        fetch("{{ route('patient.recalculate.bill') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                patient_id: "{{ $patient->id }}",
                discharge_date: discharge.value
            })
        })
        .then(res => res.json())
        .then(data => {

            // Stay Table
            let stayHTML = "";
            data.filledBills.forEach((b, i) => {
                stayHTML += `
                <tr>
                    <td class="text-center">${i+1}</td>
                    <td>${b.category}</td>
                    <td>${b.period}</td>
                    <td class="text-end">₹ ${b.total_amount.toFixed(2)}</td>
                </tr>`;
            });
            stayTable.innerHTML = stayHTML;

            // Summary Table
            summaryTable.innerHTML = `
                <tr>
                    <td>Monthly Charges</td>
                    <td class="text-end">${data.total.toFixed(2)}</td>
                    <td class="text-end">${data.received.toFixed(2)}</td>
                    <td class="text-end text-danger fw-bold">${data.pending.toFixed(2)}</td>
                </tr>
            `;

            // Totals
            document.getElementById("total_amount").textContent = data.total.toFixed(2);
            document.getElementById("pending_amount").textContent = data.pending.toFixed(2);
            document.getElementById("final_amount_box").value = data.pending.toFixed(2);
            document.getElementById("final_amount_input").value = data.pending.toFixed(2);

            document.getElementById("months_stayed").textContent = data.months;
            document.getElementById("extra_days").textContent = data.extra;
        });
    }

    // SweetAlert confirm
    document.getElementById("confirmBtn").addEventListener("click", function () {
        Swal.fire({
            title: 'Confirm Discharge?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Confirm',
            confirmButtonColor: '#28a745'
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('dischargeForm').submit();
            }
        });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dischargeInput = document.getElementById('discharge_date');

    // ✅ Save discharge date to session via AJAX
    dischargeInput.addEventListener('change', function () {
        const date = this.value;
        if (date) {
            fetch("{{ route('patient.discharge.storeDate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ discharge_date: date })
            }).then(res => res.json())
            .then(data => console.log("Discharge date stored:", data))
            .catch(err => console.error(err));
        }
    });
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#discharge_advice'));
</script>

@endsection
