<!DOCTYPE html>
<html>
<head>
    <title>Patient Bill - {{ $bill->name }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; margin: 0; background-color: #f8f8f8; color: #333; }
        .invoice-container { max-width: 900px; margin: 40px auto; background-color: #fff; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; }

        /* Header */
        .invoice-header { padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3498db; }
        .invoice-header h1 { margin: 0; font-size: 26px; color: #2c3e50; }
        .invoice-header p { margin: 5px 0 0; font-size: 14px; color: #555; }

        /* Patient Info */
        .invoice-info { padding: 20px 30px; display: flex; justify-content: space-between; flex-wrap: wrap; border-bottom: 1px solid #3498db; }
        .invoice-info div { margin-bottom: 8px; }
        .invoice-info strong { display: inline-block; width: 130px; color: #34495e; }

        /* Table with full borders */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 2px solid #3498db; /* Outer table border */
        }

        th, td {
            padding: 12px 15px;
            font-size: 14px;
            border: 1px solid #3498db; /* Cell borders */
        }

        th {
            background-color: #f0f8ff; /* Light blue header */
            color: #3498db;
            font-weight: 600;
            text-align: left;
        }

        tbody tr:nth-child(even) { background-color: #f9f9f9; }

        /* Totals */
        .totals { padding: 15px 30px; display: flex; justify-content: flex-end; font-size: 16px; font-weight: bold; border-top: 2px solid #3498db; color: #2c3e50; }

        /* Footer */
        .invoice-footer { text-align: center; padding: 15px 30px; font-size: 14px; border-top: 1px solid #3498db; color: #555; }
        .invoice-footer em { display: block; margin-top: 5px; font-style: italic; color: #777; }

        /* Buttons */
        .no-print { margin-top: 10px; }
        .btn-print { padding: 8px 16px; background-color: #3498db; color: #fff; border: none; border-radius: 5px; cursor: pointer; }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
<div class="invoice-container">

    <!-- Header -->
    <div class="invoice-header">
        <div>
            <h1>Jeevan Medical Center</h1>
            <p>Medical Billing Statement</p>
        </div>
        <div>
            <p><strong>Bill No:</strong> {{ $bill->id }}</p>
            <p><strong>Date:</strong> {{ $bill->bill_date }}</p>
        </div>
    </div>

    <!-- Patient Info -->
    <div class="invoice-info">
        <div><strong>Patient Name:</strong> {{ $bill->name }}</div>
        <div><strong>Reg No:</strong> {{ $bill->reg_no }}</div>
    </div>

    <!-- Payment Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Purpose</th>
                <th>Payment Mode</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $allPayments = json_decode($bill->split_items, true) ?? [];
                $nextPayments = json_decode($bill->next_payments, true) ?? [];
                $mergedPayments = [];

                // Split Items
                foreach($allPayments as $item){
                    $mergedPayments[] = [
                        'date' => $item['date'] ?? $bill->bill_date,
                        'purpose' => $item['remarks'] ?? $item['title'] ?? 'Service',
                        'amount' => floatval($item['amount'] ?? 0),
                        'mode' => $item['payment_mode'] ?? 'Cash'
                    ];
                }

                // Next Payments
                foreach($nextPayments as $np){
                    $mergedPayments[] = [
                        'date' => $np['date'] ?? $bill->bill_date,
                        'purpose' => $np['purpose'] ?? 'Next Payment', // ✅ Corrected purpose
                        'amount' => floatval($np['amount'] ?? 0),
                        'mode' => $np['payment_mode'] ?? 'Cash'
                    ];
                }

                $totalAmount = array_sum(array_column($mergedPayments, 'amount'));
            @endphp

            @foreach($mergedPayments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payment['date'] }}</td>
                <td>{{ $payment['purpose'] }}</td>
                <td>{{ $payment['mode'] }}</td>
                <td>₹{{ number_format($payment['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="totals">
        Total Bill Amount: ₹{{ number_format($totalAmount, 2) }}
    </div>

    <!-- Footer -->
    <div class="invoice-footer">
        <p>Thank you for choosing Jeevan Medical Center</p>
        <p class="no-print"><button class="btn-print" onclick="window.print()">Print Bill</button></p>
        <em>“மனதை நன்றாகப் பழகுங்கள், பழக்கங்களை மாற்றுங்கள்; போதைவிலக்கல் உங்கள் வாழ்க்கையை மாற்றும்!”</em>
    </div>

</div>
</body>
</html>
