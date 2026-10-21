@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Admission Details - {{ $admission->patient_id }}</h4>
                </div>

                <div class="card-body">

                    {{-- Admission Summary --}}
                    <h5 class="mb-3">Admission Summary</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3"><p><strong>Patient ID:</strong> {{ $admission->patient_id }}</p></div>
                        <div class="col-md-3"><p><strong>Patient Name:</strong> {{ $admission->full_name }}</p></div>
                        <div class="col-md-3"><p><strong>Duration:</strong> {{ $admission->duration }} Month(s)</p></div>
                        <div class="col-md-3"><p><strong>Monthly Amount:</strong> ₹ {{ number_format($admission->monthly_amount, 2) }}</p></div>
                        <div class="col-md-3"><p><strong>Initial Paid:</strong> ₹ {{ number_format($admission->initial_fees, 2) }}</p></div>
                    </div>

                    {{-- Pending Fees --}}
                    <div class="mb-3">
                        <h5>Pending Fees</h5>
                        <p class="fs-5 fw-bold text-danger">₹ {{ number_format($pendingAmount, 2) }}</p>
                    </div>

                    {{-- Add Due Button --}}
                    @if($showAddDue)
                        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addDueModal">
                            <i class="fa-solid fa-plus"></i> Add Due Payment
                        </button>
                    @endif

                    {{-- Add Due Modal --}}
                    @if($pendingAmount > 0)
                    <div class="modal fade" id="addDueModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admissions.addDue', $admission->id) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Due Payment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Due Date</label>
                                            <input type="date" name="due_date" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Amount</label>
                                            <input type="number" name="due_amount" class="form-control" max="{{ $pendingAmount }}" required>
                                            <small class="text-muted">Maximum: ₹ {{ number_format($pendingAmount, 2) }}</small>
                                        </div>
                                        <div class="mb-3">
                                            <label>Payment Mode</label>
                                            <select name="payment_mode" class="form-control" required>
                                                <option value="Cash">Cash</option>
                                                <option value="Card">Card</option>
                                                <option value="UPI">UPI</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success">Add Due</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    {{-- Due Payments Table --}}
                    <h5>Due Payments</h5>
                    @if(count($duePayments) > 0)
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($duePayments as $index => $due)
                                <tr>
                                    <td>
                                        @if($due['due_date'])
                                            {{ \Carbon\Carbon::parse($due['due_date'])->format('d-m-Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>₹ {{ number_format($due['due_amount'], 2) }}</td>
                                    <td>{{ $due['due_payment_mode'] ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDueModal{{ $index }}">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="printBill({{ $index }})">
                                            <i class="fa-solid fa-print"></i> Print Bill
                                        </button>
                                    </td>
                                </tr>

                                {{-- Edit Due Modal --}}
                                <div class="modal fade" id="editDueModal{{ $index }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admissions.updateDue', [$admission->id, $index]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Due Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Due Date</label>
                                                        <input type="date" name="due_date" class="form-control" value="{{ $due['due_date'] }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Amount</label>
                                                        <input type="number" name="due_amount" class="form-control" value="{{ $due['due_amount'] }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Payment Mode</label>
                                                        <select name="payment_mode" class="form-control" required>
                                                            <option value="Cash" {{ $due['due_payment_mode']=='Cash' ? 'selected' : '' }}>Cash</option>
                                                            <option value="Card" {{ $due['due_payment_mode']=='Card' ? 'selected' : '' }}>Card</option>
                                                            <option value="UPI" {{ $due['due_payment_mode']=='UPI' ? 'selected' : '' }}>UPI</option>
                                                            <option value="Bank Transfer" {{ $due['due_payment_mode']=='Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Due</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden Printable Bill --}}
                                <div id="bill{{ $index }}" style="display:none; padding:20px; font-family:Arial, sans-serif; width:350px;">
                                    <div style="text-align:center; border-bottom:1px solid #000; margin-bottom:10px;">
                                        <h4>Payment Receipt</h4>
                                    </div>
                                    <p><strong>Patient Name:</strong> {{ $admission->full_name }}</p>
                                    <p><strong>Patient ID:</strong> {{ $admission->patient_id }}</p>
                                    <p><strong>Due Date:</strong> 
                                        @if($due['due_date'])
                                            {{ \Carbon\Carbon::parse($due['due_date'])->format('d-m-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <p><strong>Amount:</strong> ₹ {{ number_format($due['due_amount'], 2) }}</p>
                                    <p><strong>Payment Mode:</strong> {{ $due['due_payment_mode'] ?? '-' }}</p>
                                    <hr>
                                    <p style="text-align:center;">Thank you for your payment!</p>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No due payments added yet.</p>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

<script>
function printBill(index) {
    var billContent = document.getElementById('bill'+index).innerHTML;
    var myWindow = window.open('', 'Print', 'height=500,width=1200');
    myWindow.document.write('<html><head><title>Print Bill</title></head><body>');
    myWindow.document.write(billContent);
    myWindow.document.write('</body></html>');
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}
</script>
@endsection
