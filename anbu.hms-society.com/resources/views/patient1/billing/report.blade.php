@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4">
                    <h3 class="mb-4 text-success fw-bold">Patient Billing Report</h3>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="billingReportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>IP Number</th>
                                    <th>Patient Name</th>
                                    <th>Billing Category</th>
                                    <th>Amount (₹)</th>
                                    <th>Received (₹)</th>
                                    <th>Pending (₹)</th>
                                    <th>Payment Mode</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billings as $billing)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $billing->ip_number }}</td>
                                        <td>{{ $billing->patient_name }}</td>
                                        <td>
                                            @php
                                                $categories = [
                                                    'medicines' => 'Medicines',
                                                    'blood_test' => 'Blood Test',
                                                    'scan' => 'Scan',
                                                    'other_expenses' => 'Other Expenses',
                                                    'injections_fluids' => 'Injections / Fluids'
                                                ];
                                            @endphp
                                            {{ $categories[$billing->category] ?? $billing->category }}
                                        </td>
                                        <td>₹{{ number_format($billing->amount, 2) }}</td>
                                        <td>₹{{ number_format($billing->received_amount, 2) }}</td>
                                        <td class="{{ $billing->pending_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            ₹{{ number_format($billing->pending_amount, 2) }}
                                        </td>
                                        <td>{{ $billing->payment_mode }}</td>
                                        <td>{{ \Carbon\Carbon::parse($billing->created_at)->format('d-m-Y') }}</td>
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

<!-- ✅ DataTables CSS/JS + Buttons Extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- ✅ DataTable Initialization -->
<script>
$(document).ready(function() {
    var table = $('#billingReportTable').DataTable({
        pageLength: 10,
        order: [[8, 'desc']],
        // Removed the 'l' (length dropdown)
        dom: '<"d-flex justify-content-between align-items-center mb-3"fB>rtip',
        buttons: [
            { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-sm btn-outline-primary' },
            { extend: 'csvHtml5', text: ' CSV', className: 'btn btn-sm btn-outline-success' },
            { extend: 'excelHtml5', text: ' Excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'pdfHtml5', text: ' PDF', className: 'btn btn-sm btn-outline-danger' },
            { extend: 'print', text: ' Print', className: 'btn btn-sm btn-outline-dark' }
        ]
    });

    // Align search box to left
    $('.dataTables_filter').addClass('text-start').removeClass('text-end');
});
</script>

<!-- ✅ Font Awesome for button icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

@endsection
