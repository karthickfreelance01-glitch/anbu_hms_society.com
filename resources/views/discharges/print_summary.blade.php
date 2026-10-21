<!DOCTYPE html>
<html>
<head>
    <title>Discharge Summary</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            margin: 40px;
            color: #333;
        }

        .container {
            max-width: 800px;
            background: #ffffff;
            padding: 30px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 32px;
            color: #3498db;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 16px;
            color: #555;
        }

        .title {
            font-size: 26px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        .info {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            background: #f1f1f1;
            padding: 12px;
            border-radius: 6px;
        }

        .info strong {
            color: #2c3e50;
        }

        .section {
            margin-top: 25px;
        }

        .section h3 {
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
            color: #3498db;
        }

        .section p {
            font-size: 16px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 16px;
            font-style: italic;
            color: #555;
        }

        .no-print {
            margin-top: 30px;
            text-align: center;
        }

        .no-print button {
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .no-print button:hover {
            background-color: #2980b9;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Your Hospital Name</h1>
            <p>123 Main Street, City Name | Phone: +91 12345 67890</p>
        </div>

        <div class="title">Discharge Summary</div>

        <div class="info"><strong>Patient Name:</strong> <span>{{ $discharge->patient_name }}</span></div>
        <div class="info"><strong>Patient ID:</strong> <span>{{ $discharge->patient_id }}</span></div>
        <div class="info"><strong>Age / Gender:</strong> <span>{{ $discharge->age }} / {{ $discharge->gender }}</span></div>
        <div class="info"><strong>Admission Date:</strong> <span>{{ $discharge->admission_datetime }}</span></div>
        <div class="info"><strong>Discharge Date:</strong> <span>{{ $discharge->dishcharge_datetime }}</span></div>
        <div class="info"><strong>Doctor:</strong> <span>{{ $discharge->doctor_name }}</span></div>

        <div class="section">
            <h3>Advice on Discharge</h3>
            <p>{{ $discharge->note }}</p>
        </div>

        <div class="footer">
            <p>Doctor's Signature: _________________________</p>
        </div>

        <div class="no-print">
            <button onclick="window.print()">Print Summary</button>
        </div>
    </div>
</body>
</html>
