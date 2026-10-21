<!DOCTYPE html>
<html>
<head>
    <title>Hospital Bill - {{ $bill->name }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .bill-container { max-width: 900px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }

        .bill-header { text-align: center; margin-bottom: 20px; }
        .bill-header h1 { margin: 0; font-size: 28px; color: #2c3e50; }
        .bill-header p { margin: 3px 0; font-size: 14px; color: #555; }

        .bill-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .bill-info div { font-size: 14px; }
        .bill-info strong { width: 120px; display: inline-block; color: #34495e; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #3498db; padding: 12px; text-align: left; }
        table th { background-color: #f0f8ff; color: #3498db; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }

        .totals { text-align: right; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        .footer { text-align: center; font-size: 13px; color: #777; }
        .print-btn { padding: 8px 16px; background-color: #3498db; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .print-btn:hover { background-color: #2980b9; }

        @media print {
            body { background: #fff; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
<div class="bill-container">

    <div class="bill-header">
        <h1>Jeevan Medical Center</h1>
        <p>Medical Billing Statement</p>
        <p>Phone: 098421 87007 | Address: Madurai, Tamil Nadu</p>
    </div>

    <div class="bill-info">
        <div>
            <p><strong>Bill No:</strong> {{ $bill->bill_id }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($bill->issue_date)->format('d-m-Y') }}</p>
        </div>
        <div>
            <p><strong>Patient Name:</strong> {{ $bill->name }}</p>
            <p><strong>Reg No:</strong> {{ $bill->reg_no }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Count</th>
                <th>Price (₹)</th>
                <th>Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicines as $index => $med)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $med->drug_name }}</td>
                <td>{{ $med->drug_count }}</td>
                <td>₹{{ number_format($med->price,2) }}</td>
                <td>₹{{ number_format($med->price * $med->drug_count,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        Total Amount: ₹{{ number_format($totalAmount, 2) }}
    </div>

    <div class="footer">
        <p>Thank you for choosing Jeevan Medical Center</p>
        <p class="no-print"><button class="print-btn" onclick="window.print()">Print Bill</button></p>
        <em>“மனதை நன்றாகப் பழகுங்கள், பழக்கங்களை மாற்றுங்கள்; போதைவிலக்கல் உங்கள் வாழ்க்கையை மாற்றும்!”</em>
    </div>

</div>
</body>
</html>
