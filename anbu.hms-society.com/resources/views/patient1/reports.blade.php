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

            <!-- Patient Selection -->
            <div class="card mb-4 shadow-sm rounded-4 ">
                <div class="card-body w-50">
                    <label class="fw-bold">Select Patient (Reg No / Name)</label>
                    <select id="patientSelect" class="form-select select2">
                        <option value="">-- Select Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->reg_no }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Billing Summary -->
            <div id="patientInfo" style="display:none;">
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body text-center">
                                <h5 class="fw-bold text-secondary mb-3">Billing Summary</h5>
                                <div class="row">
                                    <div class ="col-6">
                                        <p><strong>Medicine Pending:</strong> ₹<span id="medicine_total">0.00</span></p>
                                        <p><strong>Monthly Amount:</strong> ₹<span id="billing_total">0.00</span></p>
                                        <p><strong>Months of Stay:</strong> <span id="monthsDiff">0</span> month(s)</p>
                                    </div>
                                    <div class="col-6">
                                        
                                        <p><strong>Paid Amount:</strong> ₹<span id="paid_amount">0.00</span></p>
                                        <p><strong>Total Amount:</strong> ₹<span id="total_amount">0.00</span></p>
                                        <p class="text-danger fw-bold"><strong>Total Pending:</strong> ₹<span id="pending_amount">0.00</span></p>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>

                    <!-- Medicine Bills Table -->
                    <div class="col-md-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body table-responsive">
                                <h5 class="fw-bold text-secondary mb-3 text-center">Medicine Bills</h5>
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Bill ID</th>
                                            <th>Medicines</th>
                                            <th>Quantities</th>
                                            <th>Final Amount</th>
                                            <th>Initial Payment</th>
                                            <th>Pending Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody id="medicineBillTableBody">
                                        <tr><td colspan="7" class="text-center text-muted">Select a patient to view bills</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Selection -->
            <div id="noSelection" class="text-center text-muted py-5">
                <i class="fa-solid fa-user-circle fa-3x mb-2"></i>
                <h6>Select a patient to view report</h6>
            </div>

        </div>
    </div>
</div>

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
                if(res.error) { alert(res.error); return; }

                $('#noSelection').hide();
                $('#patientInfo').show();

                // Billing Summary
                $('#medicine_total').text(parseFloat(res.medicine_total).toFixed(2));
                $('#billing_total').text(parseFloat(res.billing_total).toFixed(2));
                $('#paid_amount').text(parseFloat(res.paid_amount).toFixed(2));
                $('#monthsDiff').text(res.monthsDiff);
                $('#total_amount').text(parseFloat(res.total_amount).toFixed(2));
                $('#pending_amount').text(parseFloat(res.pending_amount).toFixed(2));

                // Medicine Bills
                let html = '';
                if(res.medicineBills.length > 0){
                    $.each(res.medicineBills, function(index, bill){
                        html += `<tr>
                            <td>${index + 1}</td>
                            <td>${bill.bill_id}</td>
                            <td>${bill.drugs ?? '-'}</td>
                            <td>${bill.quantities ?? '-'}</td>
                            <td>₹${parseFloat(bill.final_amount).toFixed(2)}</td>
                            <td>₹${parseFloat(bill.initial_payment).toFixed(2)}</td>
                            <td>₹${parseFloat(bill.pending_payment).toFixed(2)}</td>
                        </tr>`;
                    });
                } else {
                    html = `<tr><td colspan="7" class="text-center text-muted">No medicine bills found</td></tr>`;
                }
                $('#medicineBillTableBody').html(html);
            },
            error: function() { alert('Error loading patient details.'); }
        });
    });
});
</script>
@endsection
