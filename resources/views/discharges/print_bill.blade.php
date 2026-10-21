<!DOCTYPE html>
<html>
<head>
    <title>Patient Bill</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
            color: #333;
        }
        .bill-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 32px;
            color: #2e7d32;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            color: #555;
            margin: 0;
        }
        .title {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 25px;
        }
        .patient-info {
            margin-bottom: 25px;
            padding: 15px;
            background: #f1f8e9;
            border-radius: 8px;
            font-size: 16px;
        }
        .patient-info p {
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 16px;
        }
        th, td {
            border: 1px solid #c8e6c9;
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #a5d6a7;
            color: #fff;
            text-transform: uppercase;
        }
        .total-row td {
            font-weight: bold;
            font-size: 18px;
            background-color: #e8f5e9;
            text-align: right;
        }
        .footer {
            text-align: right;
            font-size: 16px;
            margin-top: 40px;
            color: #555;
        }
        .print-btn {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn button {
            padding: 12px 25px;
            font-size: 16px;
            cursor: pointer;
            background-color: #2e7d32;
            color: #fff;
            border: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .print-btn button:hover {
            background-color: #1b5e20;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <div class="header">
            <h1>🏥 Your Hospital Name</h1>
            <p>123 Main Street, City Name | Phone: +91 12345 67890</p>
        </div>

        <div class="title">Patient Bill</div>

        <div class="patient-info">
            <p><strong>Patient Name:</strong> {{ $discharge->patient_name }}</p>
            <p><strong>Patient ID:</strong> {{ $discharge->patient_id }}</p>
            <p><strong>Discharge Date:</strong> {{ $discharge->dishcharge_datetime }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Particulars</th>
                    <th>Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Due Amount</td>
                    <td>₹ {{ number_format($paidAmount, 2) }}</td>
                </tr>
                <tr>
                    <td>Discharge Payment</td>
                    <td>₹ {{ number_format($pendingAmount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Amount</td>
                    <td>₹ {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Authorized Signature: ________________________</p>
        </div>

        <div class="print-btn">
            <button onclick="window.print()">Print Bill</button>
        </div>
    </div>
</body>
</html>
