<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Discharge Bill - {{ $patient->name }}</title>
  <style>
    @page { margin: 30px; }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 13px;
      background-color: #fff;
      color: #000;
    }
    .bill-container {
      border: 1px solid #ddd;
      border-top: 6px solid #009fe3 ;
      border-radius: 8px;
      padding: 20px 25px;
    }
    .header-table {
      width: 100%;
      border-bottom: 2px solid #009fe3;
      margin-bottom: 20px;
    }
    .hospital-name {
      font-weight: bold;
      font-size: 16px;
      color: #007bff;
      margin: 0;
    }
    .hospital-address {
      font-size: 10px;
      color: #555;
    }
    .bill-title {
      text-align: center;
      font-weight: bold;
      font-size: 16px;
      margin: 10px 0 25px;
      text-transform: uppercase;
    }
    .info-table {
      width: 100%;
      margin-bottom: 15px;
      border-collapse: collapse;
    }
    .info-table td {
      padding: 4px 6px;
      vertical-align: top;
    }
    .section-title {
      font-weight: bold;
      font-size: 14px;
      color: #026b07;
      border-bottom: 2px solid ##fcc008;
      display: inline-block;
      margin: 20px 0 10px;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    .table th, .table td {
      border: 1px solid #999;
      padding: 6px;
      text-align: left;
    }
    .table th {
      background-color: #eaf2ff;
      font-weight: bold;
    }
    .summary-table {
      width: 60%;
      float: right;
      border-collapse: collapse;
    }
    .summary-table th, .summary-table td {
      border: 1px solid #999;
      padding: 6px;
    }
    .final-amount {
      background-color: #e6f4ea;
      font-weight: bold;
      color: #155724;
    }
    .footer-text {
      clear: both;
      text-align: center;
      font-size: 11px;
      color: #555;
      margin-top: 30px;
      border-top: 1px dashed #ccc;
      padding-top: 8px;
    }
  </style>
</head>
<body>
  <div class="bill-container">

    <!-- Header -->
   <table class="header-table" style="width:100%; border-bottom:2px solid #fcc008; margin-bottom:25px;">
  <tr>
    <td width="35%" style="text-align:left;">
          {{-- ✅ Use public_path() for Dompdf --}}
          @php
              $logoPath = public_path('assets/img/logo.jpeg');
              $logoBase64 = '';
              if (file_exists($logoPath)) {
                  $logoBase64 = base64_encode(file_get_contents($logoPath));
              }
          @endphp
          @if($logoBase64)
              <img src="data:image/jpeg;base64,{{ $logoBase64 }}" style="width:70%;">
          @else
              <p style="color:red;">Logo not found</p>
          @endif
        </td>

    <td width="65%" style="text-align:center;">
      <div class="hospital-name" style="font-weight:800; font-size:20px; color:#026b07; text-transform:uppercase;">
    Anbu De-Addiction Rehabilitation Centre
      </div>
     
      <div class="hospital-address" style="font-size:13px; color:#000; line-height:1.5;font-weight:700">
      178, Kamarajar Salai, Karaikal, Puducherry 609602<br>
           PH: +91 87789 76391 
      </div>
    </td>

    
  </tr>
</table>


    <h4 class="bill-title">Patient Discharge Bill Summary</h4>

    <!-- Patient Info -->
    <table class="info-table" width="100%">
      <tr>
        <td width="50%">
          <table width="100%">
            <tr><td><strong>Patient Name:</strong> {{ $patient->name }}</td></tr>
            <tr><td><strong>IP Number:</strong> {{ $patient->ip_number }}</td></tr>
            <tr><td><strong>Gender:</strong> {{ $patient->gender }}</td></tr>
            <tr><td><strong>Contact:</strong> {{ $patient->contact_number }}</td></tr>
          </table>
        </td>
        <td width="50%">
          <table width="100%">
            <tr><td><strong>Admission Date:</strong> {{ $admissionDate->format('d-M-Y') }}</td></tr>
            <tr><td><strong>Discharge Date:</strong> {{ $dischargeDate->format('d-M-Y') }}</td></tr>
            <tr><td><strong>Monthly Fess:</strong> {{ $monthlyAmount }} </td></tr>
            <tr><td><strong>Extra Days:</strong> {{ $extraDays }} </td></tr>
          </table>
        </td>
      </tr>
    </table>

    <!-- Billing Details -->
    <p class="section-title">Detailed Billing </p>
    <table class="table">
      <thead>
        <tr>
          <th>Category</th>
          <th align="right">Total (₹)</th>
          <th align="right">Received (₹)</th>
          <th align="right">Pending (₹)</th>
        </tr>
      </thead>
      <tbody>
        @foreach($allBillings as $b)
        <tr>
          <td>{{ ucfirst($b->category) }}</td>
          <td style="text-align : right">{{ number_format($b->total_amount, 2) }}</td>
          <td style="text-align : right">{{ number_format($b->total_received, 2) }}</td>
          <td style="text-align : right">{{ number_format($b->total_pending, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Summary -->
    <table class="summary-table">
      <tr><th>Total Amount</th><td align="right">₹{{ number_format($totalAmount, 2) }}</td></tr>
      <tr><th>Total Received</th><td align="right">₹{{ number_format($totalReceived, 2) }}</td></tr>
      <tr><th>Total Pending</th><td align="right">₹{{ number_format($totalPending, 2) }}</td></tr>
      <tr class="final-amount"><th>Final Amount Payable</th><td align="right">₹{{ number_format($totalPending, 2) }}</td></tr>
    </table>

    <div class="footer-text">
      <p><strong>Note:</strong> Please settle pending amount before discharge. For clarifications, contact billing counter.</p>
      <p>Thank you for choosing <strong>Anbu De-Addiction Rehabilitation Centre</strong>. Wishing you a speedy recovery!</p>
    </div>

  </div>
</body>
</html>
