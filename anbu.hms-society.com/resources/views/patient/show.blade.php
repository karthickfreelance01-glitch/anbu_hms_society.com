@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="text-primary fw-bold">Patient Details</h3>
                        <p class="text-muted">
                            Complete details for patient <strong>{{ $patient->name }}</strong> 
                            (Reg No: {{ $patient->reg_no }})
                        </p>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="printDetails()">
                        <i class="fas fa-print"></i> Print Details
                    </button>
                </div>

                <!-- Print Area -->
                <div id="printArea">

                    <!-- Patient Info -->
                    <div class="card shadow-sm border-0 mb-4 rounded-4">
                        <div class="card-header bg-info text-white rounded-top-4">
                            <h5 class="mb-0">Patient Info</h5>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-6 mb-2"><strong>Reg No:</strong> <span class="badge bg-primary">{{ $patient->reg_no }}</span></div>
                            <div class="col-md-6 mb-2"><strong>Name:</strong> {{ $patient->name }}</div>
                            <div class="col-md-6 mb-2"><strong>Age:</strong> {{ $patient->age }} years</div>
                            <div class="col-md-6 mb-2"><strong>Gender:</strong> {{ $patient->gender }}</div>
                            <div class="col-md-6 mb-2"><strong>Contact:</strong> {{ $patient->contact_number }}</div>
                            <div class="col-md-6 mb-2"><strong>Admission Date:</strong> {{ $patient->date_of_admission }}</div>
                            
                            
                        </div>
                    </div>

                    <!-- Unified Payment Table -->
                    <div class="card shadow-sm border-0 mb-4 rounded-4">
                        <div class="card-header bg-warning text-white rounded-top-4">
                            <h5 class="mb-0">Payment Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Purpose</th>
                                        <th>Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach($allPayments as $index => $pay)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-start">{{ $pay['purpose'] }}</td>
                                            <td class="text-end">{{ number_format($pay['amount'], 2) }}</td>
                                        </tr>
                                        @php $total += $pay['amount']; @endphp
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-end">Overall Total</td>
                                        <td class="text-end">{{ number_format($total, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
function printDetails() {
    window.print();
}
</script>

<style>
.card-header h5 { font-weight: 600; }
.badge { font-size: 0.9rem; }
.table th, .table td { vertical-align: middle; }
.shadow-sm { box-shadow: 0 2px 6px rgba(0,0,0,0.08) !important; }
.rounded-4 { border-radius: 1rem !important; }

/* Print Styles */
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; top: 0; left: 0; width: 100%; }
}
</style>
@endsection
