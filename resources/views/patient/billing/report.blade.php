@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4 animate__animated animate__fadeIn">
                    <h3 class="mb-4 text-success fw-bold">Patient Billing Report</h3>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="billingReportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Bill ID</th>
                                    <th>IP Number</th>
                                    <th>Patient Name</th>
                                    <th>Categories</th>
                                    <th>Total Amount (₹)</th>
                                    <th>Received (₹)</th>
                                    <th>Pending (₹)</th>
                                    <th>Payment Mode</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($patients as $patient)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-bold text-primary">{{ $patient->bill_id }}</span></td>
                                    <td>{{ $patient->ip_number }}</td>
                                    <td>{{ $patient->patient_name }}</td>
                                    <td>{{ strtoupper($patient->categories ?? '-') }}</td>

                                    <td>₹{{ number_format($patient->total_amount, 2) }}</td>
                                    <td>₹{{ number_format($patient->total_received, 2) }}</td>

                                    <td class="{{ $patient->total_pending > 0 ? 'text-danger' : 'text-success' }}">
                                        ₹{{ number_format($patient->total_pending, 2) }}
                                    </td>

                                    <td>{{ $patient->payment_mode ?? '-' }}</td>

                                    <td>{{ \Carbon\Carbon::parse($patient->bill_date)->format('d-m-Y') }}</td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary view-details mb-1"
                                            data-bill="{{ $patient->bill_id }}"
                                            data-name="{{ $patient->patient_name }}"
                                            data-ip="{{ $patient->ip_number }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <a  class="btn btn-sm btn-warning print-bill mb-1"
                                            href="{{url('patient/bill/'.$patient->bill_id)}}" target="_blank">
                                            <i class="fas fa-print"></i> Print Bill
                                        </a>

                                        <button class="btn btn-sm btn-success add-payment"
                                            data-id="{{ $patient->bill_id }}" 
                                            data-name="{{ $patient->patient_name }}"
                                            data-ip="{{ $patient->ip_number }}">
                                            <i class="fas fa-plus-circle"></i> Add Payment
                                        </button>

                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div><!-- card end -->

            </div>
        </div>
    </div>
</div>

<!-- BILL DETAILS MODAL -->
<div class="modal fade" id="billingDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 shadow-lg">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Billing Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <h6><strong>IP Number:</strong> <span id="modalIpNumber"></span></h6>
        <h6><strong>Patient Name:</strong> <span id="modalPatientName"></span></h6>
        <h6><strong>Bill ID:</strong> <span id="modalBillId"></span></h6>

        <hr>

        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Category</th>
                <th>Amount (₹)</th>
                <th>Received (₹)</th>
                <th>Pending (₹)</th>
                <th>Payment Mode</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="billingDetailsBody"></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- EDIT BILL ITEM MODAL -->
<div class="modal fade" id="editBillItemModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="editBillForm">
      @csrf
      <input type="hidden" id="edit_item_id" name="item_id">
      <input type="hidden" id="json_index" name="json_index">

      <div class="modal-content rounded-4 shadow-lg">

        <div class="modal-header bg-warning">
          <h5 class="modal-title">Edit Bill Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="row g-3 mt-3">
            <div class="col-md-6">
              <label class="form-label">Bill Date</label>
              <input id="edit_bill_date" name="bill_date" type="date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Category</label>
              <select id="edit_category" name="category" class="form-select" required>
                <option value="">-- Select --</option>
                <option value="pickup">Patient Pick Charges</option>
                <option value="medicines">Medicines</option>
                <option value="blood_test">Blood Test</option>
                <option value="outside_hospital">Outside Hospital</option>
                <option value="scan">Scan</option>
                <option value="other_expenses">Other Expenses</option>
                <option value="injections_fluids">Injections / Fluids</option>
              </select>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Amount (₹)</label>
              <input id="edit_amount" name="amount" type="number" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Received (₹)</label>
              <input id="edit_received" name="received_amount" type="number" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Pending (₹)</label>
              <input id="edit_pending" name="pending_amount" type="number" step="0.01" class="form-control" required>
            </div>
          </div>

          <div class="mt-3">
            <label class="form-label">Note</label>
            <input id="edit_note" name="note" type="text" class="form-control">
          </div>

          <div class="row g-3 mt-3">
            <div class="col-md-6">
              <label class="form-label">Account Type</label>
              <select id="edit_account_type" name="account_type" class="form-select">
                <option value="">-- Select --</option>
                <option>Cash</option>
                <option>Savings-SBI</option>
                <option>Savings_TMB</option>
                <option>Current account</option>
                <option>Others</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Payment Mode</label>
              <select id="edit_payment_mode" name="payment_mode" class="form-select">
                <option value="">-- Select --</option>
                <option>Cash</option>
                <option>Card</option>
                <option>UPI</option>
                <option>NetBanking</option>
                <option>Others</option>
              </select>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>

      </div>
    </form>
  </div>
</div>

<!-- ADD PAYMENT MODAL -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                        <h6><strong>IP Number:</strong> <span id="modal_ip"></span></h6>
                        <h6><strong>Patient Name:</strong> <span id="modal_name"></span></h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pending Category</label>
                        <select name="category" id="pendingCategory" class="form-select" required></select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pending Amount (₹)</label>
                            <input type="text" id="pending_amount" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Received Amount (₹)</label>
                            <input type="number" step="0.01" name="received_amount" id="received_amount" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Payment Mode</label>
                            <select name="payment_mode" class="form-select" required>
                                <option value="">-- Select Mode --</option>
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="UPI">UPI</option>
                                <option value="NetBanking">Net Banking</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
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

<!-- DATATABLE + SCRIPTS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
$(function() {

    $('#billingReportTable').DataTable({
        pageLength: 10,
        order: [[1, 'desc']]
    });

    // VIEW BILL DETAILS
    $(document).on('click', '.view-details', function() {
        
        const billId = $(this).data('bill');
        const patientName = $(this).data('name');
        const ipNumber = $(this).data('ip');

        $('#modalBillId').text(billId);
        $('#modalPatientName').text(patientName);
        $('#modalIpNumber').text(ipNumber);

        $('#billingDetailsBody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
        $('#billingDetailsModal').modal('show');

        $.ajax({
            url: "{{ url('patient-billing-details') }}/" + billId,
            method: "GET",
            success: function(data) {

                let rows = '';

                if (!data || data.length === 0) {
                    rows = '<tr><td colspan="7" class="text-center text-muted">No records found.</td></tr>';
                } else {
                    data.forEach((item, index) => {

                        let payments = item.payment_history?.length
                            ? item.payment_history
                            : [{
                                amount: item.received_amount,
                                date: item.payment_date,
                                payment_mode: item.payment_mode
                            }];

                        payments.forEach(p => {

                            let formattedDate = p.date
                                ? new Date(p.date).toLocaleDateString('en-GB')
                                : '-';

                            rows += `
                                <tr data-id="${item.id}" data-index="${index}">
                                    <td>${(item.category || '').replace('_',' ').toUpperCase()}</td>
                                    <td>₹${parseFloat(item.amount || 0).toFixed(2)}</td>
                                    <td>₹${parseFloat(p.amount || 0).toFixed(2)}</td>
                                    <td class="${(item.amount - p.amount) > 0 ? 'text-danger' : 'text-success'}">
                                        ₹${parseFloat(item.amount - p.amount).toFixed(2)}
                                    </td>
                                    <td>${p.payment_mode || '-'}</td>
                                    <td>${formattedDate}</td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning edit-bill-item"
                                            data-id="${item.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger delete-bill-item" data-id="${item.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                    });
                }

                $('#billingDetailsBody').html(rows);
            },
            error: function() {
                $('#billingDetailsBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>');
            }
        });

    });

    // OPEN EDIT BILL ITEM MODAL
    $(document).on("click", ".edit-bill-item", function () {

        let row = $(this).closest("tr");

        $("#edit_item_id").val($(this).data("id"));
        $("#json_index").val(row.data("index"));

        let category = row.find("td:eq(0)").text().trim();
        let amount = row.find("td:eq(1)").text().replace("₹","").trim();
        let received = row.find("td:eq(2)").text().replace("₹","").trim();
        let pending = row.find("td:eq(3)").text().replace("₹","").trim();
        let paymentMode = row.find("td:eq(4)").text().trim();
        let date = row.find("td:eq(5)").text().trim();

        let parts = date.includes("/") ? date.split("/") : ["", "", ""];
        let formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];

        $("#edit_amount").val(amount);
        $("#edit_received").val(received);
        $("#edit_pending").val(pending);
        $("#edit_payment_mode").val(paymentMode);
        $("#edit_bill_date").val(formattedDate);
        $("#edit_category").val(category.toLowerCase().replace(/ /g, "_"));

        $("#editBillItemModal").modal("show");
    });

    // LIVE CALCULATE PENDING
    $(document).on('input', '#edit_amount, #edit_received', function() {
        const a = parseFloat($('#edit_amount').val()) || 0;
        const r = parseFloat($('#edit_received').val()) || 0;
        $('#edit_pending').val((a - r).toFixed(2));
    });

    // SAVE EDITED BILL ITEM
    $('#editBillForm').submit(function(e) {
        e.preventDefault();

        const id = $('#edit_item_id').val();

        $.post("{{ url('update-patient-billing-item') }}/" + id, $(this).serialize())
        .done(function(res) {
            alert(res.message);
            location.reload();
        })
        .fail(function(xhr) {
            alert(xhr.responseJSON?.message || "Update failed!");
        });
    });

    // DELETE BILL ITEM
    $(document).on('click', '.delete-bill-item', function() {
        if (!confirm('Are you sure?')) return;

        const id = $(this).data('id');

        $.ajax({
            url: "{{ url('delete-patient-billing-item') }}/" + id,
            type: "DELETE",
            data: {_token: "{{ csrf_token() }}"},
            success: function() {
                alert("Deleted!");
                location.reload();
            }
        });
    });

    // ADD PAYMENT
    $(document).on('click', '.add-payment', function () {

        let billId = $(this).data('id');

        $('#pendingCategory').html('<option>Loading...</option>');
        $('#paymentForm')[0].reset();

        $('#modal_ip').text($(this).data('ip'));
        $('#modal_name').text($(this).data('name'));

        $('#addPaymentModal').modal('show');

        if (!$('#billing_id').length) {
            $('<input>', {
                type: 'hidden',
                id: 'billing_id',
                name: 'billing_id',
                value: billId
            }).appendTo('#paymentForm');
        } else {
            $('#billing_id').val(billId);
        }

        $.get("{{ url('patient-pending-category-by-id') }}/" + billId, function (data) {

            if (!data.length) {
                $('#pendingCategory').html('<option value="">No Pending</option>');
                $('#pending_amount').val('');
                return;
            }

            let options = '<option value="">-- Select --</option>';

            data.forEach(item => {
                options += `
                    <option 
                        value="${item.category}" 
                        data-pending="${item.pending_amount}">
                        ${item.category.replace('_', ' ').toUpperCase()} — ₹${parseFloat(item.pending_amount).toFixed(2)}
                    </option>`;
            });

            $('#pendingCategory').html(options);
        });

    });

    // SHOW PENDING AMOUNT
    $(document).on('change', '#pendingCategory', function() {
        $('#pending_amount').val(
            parseFloat($(this).find(':selected').data('pending') || 0).toFixed(2)
        );
    });

    // SAVE PAYMENT
    $('#paymentForm').submit(function(e) {
        e.preventDefault();

        $.post("{{ url('add-patient-payment') }}", $(this).serialize())
        .done(function(res) {
            alert(res.message);
            $('#addPaymentModal').modal('hide');
            location.reload();
        })
        .fail(function(xhr) {

            let msg = "Something went wrong";

            if (xhr.responseJSON?.errors) {
                msg = Object.values(xhr.responseJSON.errors).join("\n");
            } else if (xhr.responseJSON?.message) {
                msg = xhr.responseJSON.message;
            }

            alert(msg);
        });

    });
    
    function deletePaymentItem(billId, itemId) {

    $.ajax({
        url: "/delete-payment/" + billId,
        type: "POST",
        data: {
            bill_item_id: itemId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            alert(res.message);
            location.reload();
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || "Delete failed!");
        }
    });
}


});
</script>

@endsection
