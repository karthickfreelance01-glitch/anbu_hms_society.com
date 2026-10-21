<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Bill - {{ $bill->patient_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; background: #fff; color: #000; font-size: 14px; }
        .bill-container { max-width: 800px; margin: 30px auto; border: 1px solid #ccc; padding: 30px; border-radius: 10px; }
        .bill-header { text-align: center; border-bottom: 2px solid #198754; padding-bottom: 10px; margin-bottom: 20px; }
        .bill-header h4 { margin: 0; color: #198754; font-weight: bold; }
        .bill-header p { margin: 0; font-size: 13px; }
        .table th, .table td { vertical-align: middle; }
        .amount-summary td { font-weight: bold; }
        .footer-note { margin-top: 30px; text-align: center; font-size: 13px; color: #666; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body>
<div class="bill-container">
    <div class="bill-header">
        <img src="{{ asset('assets/img/logo.jpeg') }}" width="10%">
        <h4>  Anbu De-Addiction Rehabilitation Centre</h4>
        <p>178, Kamarajar Salai, Karaikal, Puducherry 609602<br>
           PH: +91 87789 76391</p>
        <p><strong>Patient Bill</strong></p>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <p><strong>Patient Name:</strong> {{ $bill->patient_name }}</p>
            <p><strong>IP Number:</strong> {{ $bill->ip_number }}</p>
        </div>
        <div class="col-md-6 text-end">
            <p><strong>Bill Date:</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y') }}</p>
            <p><strong>Bill No:</strong> {{ $bill->bill_id }}</p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Amount (₹)</th>
                <th>Received (₹)</th>
                <th>Pending (₹)</th>
                <th>Payment Mode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billingRecords as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td>₹{{ number_format($item->amount, 2) }}</td>
                    <td>₹{{ number_format($item->received_amount, 2) }}</td>
                    <td>₹{{ number_format($item->amount - $item->received_amount, 2) }}</td>
                    <td>{{ $item->payment_mode ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="table amount-summary">
        <tr><td>Total Amount</td><td class="text-end">₹{{ number_format($bill->total_amount, 2) }}</td></tr>
        <tr><td>Total Received</td><td class="text-end text-success">₹{{ number_format($bill->total_received, 2) }}</td></tr>
        <tr><td>Total Pending</td><td class="text-end text-danger">₹{{ number_format($bill->total_pending, 2) }}</td></tr>
    </table>

    <div class="footer-note">
        <p>Thank you for choosing <b>Anbu De-Addiction Rehabilitation Centre</b>. Wishing you a speedy recovery!</p>
        <button class="btn btn-success no-print" onclick="window.print()">🖨 Print Bill</button>
        <a href="{{ url('patient/billing/reports') }}" class="btn btn-secondary no-print">⬅ Back</a>
    </div>
</div>
</body>
</html>
