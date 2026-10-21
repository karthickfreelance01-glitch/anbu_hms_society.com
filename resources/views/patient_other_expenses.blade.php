@extends('layout.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-primary">Patient Other Expenses</h4>
                    <button class="btn btn-gradient-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="bi bi-plus-circle"></i> Add Expense
                    </button>
                </div>

                <!-- Filter Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Patient</label>
                                <select id="filter_patient" class="form-select">
                                    <option value="">All</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->name }}">{{ $patient->name }} ({{ $patient->reg_no }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" id="filter_from" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" id="filter_to" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select id="filter_status" class="form-select">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="finished">Finished</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button id="filterBtn" class="btn btn-secondary w-50">Filter</button>
                                <button id="resetBtn" class="btn btn-light w-50">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses Table -->
                <div class="card shadow-lg rounded-4 p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="expenseTable">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Reg No</th>
                                    <th>Patient Name</th>
                                    <th>Date</th>
                                    <th>Total (₹)</th>
                                    <th>Paid (₹)</th>
                                    <th>Pending (₹)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $p)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $p->reg_no }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td>{{ $p->date }}</td>
                                    <td class="total_amount">{{ $p->amount_given }}</td>
                                    <td class="paid_amount">{{ $p->amount_received }}</td>
                                    <td class="pending_amount">{{ $p->pending_amount }}</td>
                                    <td>
                                        @if($p->pending_amount > 0)
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-success">Finished</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($p->pending_amount > 0)
                                            <button class="btn btn-sm btn-info addPaymentBtn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#addPaymentModal"
                                                data-id="{{ $p->id }}"
                                                data-pending="{{ $p->pending_amount }}">
                                                Add Payment
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-secondary viewPaymentBtn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewPaymentModal"
                                            data-payments='@json($p->payments_list)'>
                                            View
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light text-center fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">Overall Totals:</td>
                                    <td id="overallTotal">0</td>
                                    <td id="overallPaid">0</td>
                                    <td id="overallPending">0</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addExpenseForm" action="{{ url('expense-store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-gradient text-white">
                <h5 class="modal-title">Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Patient</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->reg_no }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Total Amount (₹)</label>
                    <input type="number" name="total_amount" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Remarks</label>
                    <textarea name="notes" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary w-100">Save Expense</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addPaymentForm" class="modal-content">
            @csrf
            <input type="hidden" id="expense_id" name="expense_id">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Payment Amount (₹)</label>
                    <input type="number" id="payment_amount" name="payment_amount" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success w-100">Save Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Payment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewPaymentBody"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function(){

    // Initialize DataTable
    var table = $('#expenseTable').DataTable({
        dom: "<'row mb-2'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true,
        order: [[3, 'desc']],
        drawCallback: updateTotals
    });

    // Custom Filters
    $.fn.dataTable.ext.search.push(function(settings, data){
        var patient = $('#filter_patient').val().toLowerCase();
        var from = $('#filter_from').val();
        var to = $('#filter_to').val();
        var status = $('#filter_status').val().toLowerCase();
        var rowPatient = data[2].toLowerCase();
        var rowDate = data[3];
        var rowStatus = data[7].toLowerCase();
        if (patient && rowPatient.indexOf(patient) === -1) return false;
        if (status && rowStatus != status) return false;
        if (from && rowDate < from) return false;
        if (to && rowDate > to) return false;
        return true;
    });

    $('#filterBtn').click(() => table.draw());
    $('#resetBtn').click(() => { $('#filter_patient,#filter_from,#filter_to,#filter_status').val(''); table.draw(); });

    // Totals
    function updateTotals(){
        let total=0, paid=0, pending=0;
        $('#expenseTable tbody tr:visible').each(function(){
            total += parseFloat($(this).find('.total_amount').text()) || 0;
            paid += parseFloat($(this).find('.paid_amount').text()) || 0;
            pending += parseFloat($(this).find('.pending_amount').text()) || 0;
        });
        $('#overallTotal').text(total.toFixed(2));
        $('#overallPaid').text(paid.toFixed(2));
        $('#overallPending').text(pending.toFixed(2));
    }

        // Add Payment Modal
    $('#addPaymentModal').on('show.bs.modal', function(e){
        var btn = $(e.relatedTarget);
        var pending = parseFloat(btn.data('pending')) || 0;
        $('#expense_id').val(btn.data('id'));
        $('#payment_amount').attr('max', pending);
        $('#payment_amount').val(''); // reset input
    });

    // Prevent typing more than pending
    $('#payment_amount').on('input', function(){
        var max = parseFloat($(this).attr('max'));
        var val = parseFloat($(this).val());
        if (val > max) {
            $(this).val(max);
            Swal.fire({
                icon: 'warning',
                title: 'Limit Exceeded',
                text: `You cannot enter more than ₹${max.toFixed(2)}.`,
                timer: 1500,
                showConfirmButton: false
            });
        }
    });


    // Save Payment
    $('#addPaymentForm').submit(function(e){
        e.preventDefault();
        var id = $('#expense_id').val();
        $.post('expense-pay/' + id, $(this).serialize())
        .done(function(res){
            $('#addPaymentModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Payment Added',
                text: res.message,
                confirmButtonText: 'OK'
            }).then(() => location.reload());
        });
    });

    // Save Expense
    $('#addExpenseForm').submit(function(e){
        e.preventDefault();
        $.post($(this).attr('action'), $(this).serialize())
        .done(function(res){
            $('#addExpenseModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: res.message,
                confirmButtonText: 'OK'
            }).then(() => location.reload());
        });
    });

    // View Payment History
    $('#viewPaymentModal').on('show.bs.modal', function(e){
        var payments = $(e.relatedTarget).data('payments');
        var html = '';
        if (payments && payments.length) {
            html += `<table class='table table-sm table-bordered'><thead><tr><th>Date</th><th>Amount (₹)</th></tr></thead><tbody>`;
            payments.forEach(p => {
                html += `<tr><td>${p.date}</td><td>${parseFloat(p.amount).toFixed(2)}</td></tr>`;
            });
            html += `</tbody></table>`;
        } else html = '<div class="text-center text-muted">No Payments</div>';
        $('#viewPaymentBody').html(html);
    });

    updateTotals();
});
</script>

<style>
.btn-gradient-primary { background: linear-gradient(90deg,#4e54c8,#8f94fb); color:#fff; border:none; }
.btn-gradient-primary:hover { opacity:.9; }
#expenseTable tbody tr:hover { background-color:#f7f7f7; }
.card { border-radius:12px; }
.badge { font-size:0.85rem; padding:0.4em 0.6em; }
</style>
@endsection
