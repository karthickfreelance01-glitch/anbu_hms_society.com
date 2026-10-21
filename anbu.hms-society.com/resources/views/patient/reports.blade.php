@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">

            <div class="d-flex justify-content-between mb-3">
                <h4>Patient Billing Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>

            <!-- 🔹 Patient Selection -->
            <div class="card mb-4 shadow-sm rounded-4">
                <div class="card-body w-50">
                    <label class="fw-bold">Select Patient (Reg No / Name)</label>
                    <select id="patientSelect" class="form-select select2">
                        <option value="">-- Select Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->ip_number }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🔹 Billing Summary -->
            <div id="patientInfo" style="display:none;">
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body">
                                <h5 class="fw-bold text-center text-secondary mb-3">Patient Billing Summary</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Patient Name:</strong> <span id="patient_name"></span></p>
                                        <p><strong>Admission Date:</strong> <span id="admission_date"></span></p>
                                        <p><strong>Discharge Date:</strong> <span id="discharge_date"></span></p>
                                        <p><strong>Months Stayed:</strong> <span id="monthsStayed">0</span> month(s)</p>
                                        <p><strong>Extra Days:</strong> <span id="extraDays">0</span> day(s)</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p>
                                            <strong>Monthly Amount:</strong>
                                            ₹<span id="monthlyTotalAmount">0.00</span>
                                            <small class="text-muted">
                                                (<span id="monthsCount">0</span> × ₹<span id="monthlyAmountEach">0.00</span>/month)
                                            </small>
                                        </p>
                                        <p>
                                            <strong>Extra Days Amount:</strong>
                                            ₹<span id="extraDaysAmount">0.00</span>
                                            <small class="text-muted">
                                                (<span id="extraDaysCount">0</span> × ₹<span id="perDayAmountEach">0.00</span>/day)
                                            </small>
                                        </p>
                                        <p><strong>Pickup Charges:</strong> ₹<span id="pickupAmount">0.00</span></p>
                                        <p><strong>Received Amount:</strong> ₹<span id="totalReceived">0.00</span></p>
                                        <p class="fw-bold text-danger"><strong>Total Pending:</strong> ₹<span id="totalPending">0.00</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 🔹 Detailed Billing Table -->
                    <div class="col-md-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body table-responsive">
                                <h5 class="fw-bold text-secondary mb-3 text-center">Category-wise Billing</h5>
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Total Amount (₹)</th>
                                            <th>Received (₹)</th>
                                            <th>Pending (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="billingTableBody">
                                        <tr><td colspan="5" class="text-center text-muted">Select a patient to view report</td></tr>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="fw-bold">
                                            <td colspan="2" class="text-end">Grand Total:</td>
                                            <td>₹<span id="totalAmount">0.00</span></td>
                                            <td>₹<span id="footerReceived">0.00</span></td>
                                            <td>₹<span id="footerPending">0.00</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🔹 No Selection -->
            <div id="noSelection" class="text-center text-muted py-5">
                <i class="fa-solid fa-user-circle fa-3x mb-2"></i>
                <h6>Select a patient to view report</h6>
            </div>

        </div>
    </div>
</div>

<!-- 🔹 Scripts -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#patientSelect').change(function() {
        const id = $(this).val();
        if (!id) {
            $('#patientInfo').hide();
            $('#noSelection').show();
            return;
        }

        $.ajax({
            url: "{{ url('patient/reports') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.error) {
                    alert(res.error);
                    return;
                }

                $('#noSelection').hide();
                $('#patientInfo').show();

                // 🔹 Patient Info
                $('#patient_name').text(res.patient.name);
                $('#admission_date').text(res.admissionDate);
                $('#discharge_date').text(res.dischargeDate);
                $('#monthsStayed').text(res.monthsStayed);
                $('#extraDays').text(res.extraDays);

                // 🔹 Billing Summary
                $('#monthlyTotalAmount').text((parseFloat(res.monthlyTotalAmount) || 0).toFixed(2));
                $('#monthsCount').text(res.monthsStayed);
                $('#monthlyAmountEach').text((parseFloat(res.monthlyAmount) || 0).toFixed(2));

                $('#extraDaysAmount').text((parseFloat(res.extraDaysAmount) || 0).toFixed(2));
                $('#extraDaysCount').text(res.extraDays);
                $('#perDayAmountEach').text((parseFloat(res.perDayAmount) || 0).toFixed(2));

                $('#pickupAmount').text((parseFloat(res.pickupAmount) || 0).toFixed(2));
                $('#totalReceived').text((parseFloat(res.totalReceived) || 0).toFixed(2));
                $('#totalPending').text((parseFloat(res.totalPending) || 0).toFixed(2));

                // 🔹 Detailed Table
                let html = '';
                if (res.allBillings && res.allBillings.length > 0) {
                    $.each(res.allBillings, function(i, item) {
                        html += `<tr>
                            <td>${i + 1}</td>
                            <td>${item.category}</td>
                            <td>₹${(parseFloat(item.total_amount) || 0).toFixed(2)}</td>
                            <td>₹${(parseFloat(item.total_received) || 0).toFixed(2)}</td>
                            <td>₹${(parseFloat(item.total_pending) || 0).toFixed(2)}</td>
                        </tr>`;
                    });
                } else {
                    html = `<tr><td colspan="5" class="text-center text-muted">No billing data found</td></tr>`;
                }

                $('#billingTableBody').html(html);

                // 🔹 Footer Totals
                $('#totalAmount').text((parseFloat(res.totalAmount) || 0).toFixed(2));
                $('#footerReceived').text((parseFloat(res.totalReceived) || 0).toFixed(2));
                $('#footerPending').text((parseFloat(res.totalPending) || 0).toFixed(2));
            },
            error: function() {
                alert('Error loading patient details.');
            }
        });
    });
});
</script>
@endsection
