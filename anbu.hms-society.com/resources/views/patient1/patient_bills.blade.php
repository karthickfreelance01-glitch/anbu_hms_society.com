@php
use Carbon\Carbon;
@endphp

@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')


    <div class="page-wrapper">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                   
                    <!-- Right DataTable Panel -->
                    <div class="col-lg-12 col-md-8">
                        <div class="card shadow-sm border-0 rounded-4 p-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Patient Bill Reports</h4>
                        
                    </div>

                    <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="billingTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>IP Number</th>
                                            <th>Patient Name</th>
                                            <th>Date of Admission</th>
                                            <th>Monthly Amount (₹)</th>
                                            <th>Received (₹)</th>
                                            <th>Pending (₹)</th>
                                            <th>Payment Mode</th>
                                            <th>View Details</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reports as $bill)
                                        @php
                                            $admissionDate = Carbon::parse($bill->date_of_admission);
                                            $currentDate = Carbon::now();
                                            
                                            // Calculate months difference (including partial month as full month)
                                            $months = $admissionDate->diffInMonths($currentDate) + 1; 
                                            $totalAmount = $bill->monthly_amount * $months;
                                            $pending = $totalAmount - $bill->received_amount;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $bill->ip_number }}</td>
                                            <td>{{ $bill->name }}</td>
                                            <td>{{ $admissionDate->format('d-m-Y') }}</td>
                                            <td>₹{{ number_format($bill->monthly_amount, 2) }} × {{ $months }} mo</td>
                                            <td>₹{{ number_format($bill->received_amount, 2) }}</td>
                                            <td class="{{ $pending > 0 ? 'text-danger' : 'text-success' }}">
                                                ₹{{ number_format($pending, 2) }}
                                            </td>
                                            <td>{{ $bill->payment_mode }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm" 
                                                        onclick="showPaymentHistoryModal({{ $bill->patient_id }}, '{{ $bill->payments ?? '[]' }}')">
                                                    View Details
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="showAddPaymentModal({{ $bill->patient_id }})">
                                                    Add Payment
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="paymentForm">
            @csrf
            <input type="hidden" name="patient_id" id="modal_patient_id">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Received Amount (₹)</label>
                        <input type="number" step="0.01" name="received_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Mode</label>
                        <select name="payment_mode" class="form-select" required>
                            <option value="">-- Select Mode --</option>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="UPI">UPI</option>
                            <option value="NetBanking">Net Banking</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Enter remarks (optional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">Save Payment</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Payment History</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Amount (₹)</th>
                                <th>Payment Date</th>
                                <th>Mode</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS & DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function(){

    let table = $('#billingTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[3,'desc']],
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        pageLength: 10
    });

    // Filter/Search Left Panel
    $('#filterForm').on('submit', function(e){
        e.preventDefault();
        table.columns(1).search($('input[name="ip_number"]').val());
        table.columns(2).search($('input[name="name"]').val());
        table.draw();
    });

    $('#resetFilter').on('click', function(){
        $('#filterForm')[0].reset();
        table.columns().search('').draw();
    });
});

// Show modals
function showAddPaymentModal(patientId){
    $('#modal_patient_id').val(patientId);
    $('#addPaymentModal').modal('show');
}

function showPaymentHistoryModal(patientId, paymentsJson){
    let payments = [];
    if(paymentsJson){
        payments = JSON.parse(paymentsJson.replace(/&quot;/g,'"'));
    }
    let tbody = '';
    payments.forEach((p,index)=>{
        tbody += `<tr>
                    <td>${index+1}</td>
                    <td>₹${parseFloat(p.amount).toFixed(2)}</td>
                    <td>${p.date}</td>
                    <td>${p.mode}</td>
                    <td>${p.remarks ?? ''}</td>
                  </tr>`;
    });
    $('#historyBody').html(tbody);
    $('#paymentHistoryModal').modal('show');
}

// Handle Add Payment form
$('#paymentForm').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: "{{ url('billing/add-payment') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res){
            Swal.fire({ icon:'success', title:'Payment Added', text:res.message }).then(()=>location.reload());
        },
        error: function(xhr){
            Swal.fire({ icon:'error', title:'Error', text:xhr.responseText });
        }
    });
});
</script>

@endsection
