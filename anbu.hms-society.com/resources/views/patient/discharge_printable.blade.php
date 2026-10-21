<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Patient Discharge Summary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    @page { size: A4; margin: 18mm; }
    body { font-family: Arial, sans-serif; color: #111; margin: 0; background: #fff; }
    .container { max-width: 900px; margin: 0 auto; }

    /* ---------- HEADER ---------- */
     .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 6px;
      margin-bottom: 10px;
    }

    .header img { width: 15%; height: auto; }

    .header-center { text-align: center; flex-grow: 1; }

    .hospital-name {
      font-family: "Elephant", Arial, sans-serif;
      color: #026b07;
      font-size: 28px;
      font-weight: 900;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .hospital-sub {
      color: #fcc008;
      font-size: 19px;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .hospital-address {
      color: #000;
      font-size: 14px;
      margin-top: 3px;
      line-height: 1.4;
    }

    .doc-title {
      text-align: center;
      color: #009fe3;
      font-weight: 700;
      font-size: 18px;
      margin: 30px 0 14px;
      text-decoration: underline;
      letter-spacing: 0.3px;
    }

    /* ---------- TABLE STYLES ---------- */
    table.std { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.std th, table.std td { padding: 6px 8px; font-size: 15px; vertical-align: top; }
    table.std th { width: 30%; font-weight: 700; color: #333; position: relative; padding-right: 25px; white-space: nowrap; }
    table.std th::after { content: ":"; position: absolute; right: 10px; top: 6px; }

    /* ---------- BILL TABLE ---------- */
    table.bill { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.bill th, table.bill td { border: 1px solid #ccc; padding: 8px 10px; font-size: 15px; }
    table.bill th { background: #f7f7f7; text-align: left; color: #333; }
    
    /* ---------- MISC STYLES ---------- */
    .signature { margin-top: 36px; display: flex; justify-content: space-between; }
    .signature div { text-align: center; width: 45%; font-weight: bold; }

    .advice-box { padding: 18px 20px; min-height: 360px;  }

    .page { page-break-after: always; }

    .double-line { width: 100%; height: 5px; position: relative; margin: 0; }
    .double-line::before, .double-line::after { content: ""; position: absolute; left: 0; width: 100%; height: 2px; }
    .double-line::before { background-color: #980106; top: 0; }
    .double-line::after { background-color: #fac012; top: 3px; }

    /* ---------- BUTTONS ---------- */
    .print-btn, .back-btn {
      position: fixed; z-index: 9999; border: none; border-radius: 30px;
      padding: 14px 24px; font-weight: bold; font-size: 15px;
      color: white; cursor: pointer; transition: 0.3s;
      box-shadow: 0 6px 15px rgba(255,0,0,0.3);
    }
    .print-btn { right: 30px; bottom: 30px; background: #a40103; }
    .back-btn { left: 30px; bottom: 30px; background: #6b50a1; }
    .print-btn:hover, .back-btn:hover { transform: scale(1.05); }
    @media print { .print-btn, .back-btn { display: none !important; } }
  </style>
</head>

<body>
<div class="container">

  {{-- PAGE 1: DISCHARGE FORM --}}
  <div class="page">
    <div class="header">
        <img src="{{ asset('assets/img/logo.jpeg') }}" width="20%">
        <div class="header-center">
          <div class="hospital-name">Anbu De-Addiction Rehabilitation Centre </div>
          <div class="hospital-address">
            <span style="color: #002b7c">
           178, Kamarajar Salai, Karaikal, Puducherry 609602<br>
           PH: +91 87789 76391 
            </span>
          </div>
        </div>
      </div>

    <div class="double-line"></div>
    <div class="doc-title">DISCHARGE FORM</div>

    <table class="std">
      <tr><th>Name</th><td>{{ $patient->name }}</td></tr>
      <tr><th>Age / Gender</th><td>{{ $patient->age }} / {{ $patient->gender }}</td></tr>
      <tr><th>IP No</th><td>{{ $patient->ip_number }}</td></tr>
      <tr><th>Treatment Period</th><td>From: {{ $admissionDate->format('d-m-Y') }} To: {{ $dischargeDate->format('d-m-Y') }}</td></tr>
      <tr><th>Address</th><td>{{ $patient->present_address }}</td></tr>
      <tr><th>Phone</th><td>{{ $patient->contact_number }}</td></tr>
      <tr><th>Guardian</th><td>{{ $patient->admitted_by }}</td></tr>
    </table>

    <div style="font-size:15px; line-height:1.8; text-align:justify; margin-top:20px;">
      I/We hereby agree to come for follow up at <strong style="color:#036d08;">Amends De-addiction Centre</strong> 
      for counselling and medications. I/We agree the patient is in good condition at discharge 
      and take full responsibility thereafter.
    </div>

    <div class="signature">
      <div>Patient Signature</div>
      <div>Guardian Signature</div>
    </div>
  </div>

  {{-- PAGE 2: DISCHARGE ADVICE --}}
  <div class="page">
    <div class="header">
        <img src="{{ asset('assets/img/logo.jpeg') }}" width="20%">
        <div class="header-center">
          <div class="hospital-name">Anbu De-Addiction Rehabilitation Centre </div>
          <div class="hospital-address">
            <span style="color: #002b7c">
           178, Kamarajar Salai, Karaikal, Puducherry 609602<br>
           PH: +91 87789 76391 
            </span>
          </div>
        </div>
      </div>

    <div class="double-line"></div>
    <div class="doc-title">DISCHARGE ADVICE</div>

    <div class="advice-box">
      <h4 style="color:#6b50a1; margin-bottom:10px;">Advice & Follow-up Instructions</h4>
      <pre style="white-space:pre-wrap; font-size:16px; line-height:1.7; color:#222;">
{{ strip_tags(str_replace(['</li>', '</p>', '<br>', '<br/>', '<br />'], "\n", $patient->discharge_advice)) }}
      </pre>
    </div>
  </div>

  {{-- PAGE 3: SUMMARY CASH BILL --}}
  <div class="page">
<div class="header">
        <img src="{{ asset('assets/img/logo.jpeg') }}" width="20%">
        <div class="header-center">
          <div class="hospital-name">Anbu De-Addiction Rehabilitation Centre </div>
          <div class="hospital-address">
            <span style="color: #002b7c">
           178, Kamarajar Salai, Karaikal, Puducherry 609602<br>
           PH: +91 87789 76391 
            </span>
          </div>
        </div>
      </div>

  <div class="double-line"></div>
  <div class="doc-title">SUMMARY CASH BILL</div>

  {{-- 🌙 MONTHLY STAY BREAKDOWN FIRST --}}
  <h4 style="margin-top:20px;">Monthly Stay Breakdown</h4>
  <table class="bill">
    <thead>
      <tr>
        <th>Month</th>
        <th style="text-align:right;">Amount (₹)</th>
        <th>Duration</th>
      </tr>
    </thead>
    <tbody>
      @php $monthlyTotal = 0; @endphp
      @foreach ($monthlyBills as $item)
        @php $monthlyTotal += $item['amount']; @endphp
        <tr>
          <td>{{ $item['label'] }}</td>
          <td style="text-align:right;">₹ {{ number_format($item['amount'], 2) }}</td>
          <td>{{ $item['from'] }} to {{ $item['to'] }}</td>
        </tr>
      @endforeach
      <tr style="background:#f5f5f5; font-weight:bold;">
        <td style="text-align:right;">Total Monthly Amount</td>
        <td style="text-align:right;">₹ {{ number_format($monthlyTotal, 2) }}</td>
        <td></td>
      </tr>
    </tbody>
  </table>

  {{-- 🌟 CATEGORIES NEXT --}}
  <h4 style="margin-top:30px;">Additional Charges</h4>
  <table class="bill">
    <thead>
      <tr>
        <th>S.No</th>
        <th>Category</th>
        <th style="text-align:right;">Amount (₹)</th>
      </tr>
    </thead>
    <tbody>
      @php
        $categories = [
          ['label' => "MEDICINES", 'amount' => optional($allBillings->firstWhere('category','medicines'))->total_amount ?? 0],
          ['label' => "PATIENT PICK CHARGES", 'amount' => optional($allBillings->firstWhere('category','pickup'))->total_amount ?? 0],
          ['label' => "BLOOD TEST", 'amount' => optional($allBillings->firstWhere('category','blood_test'))->total_amount ?? 0],
          ['label' => "SCAN", 'amount' => optional($allBillings->firstWhere('category','scan'))->total_amount ?? 0],
          ['label' => "OUTSIDE HOSPITAL", 'amount' => optional($allBillings->firstWhere('category','outside_hospital'))->total_amount ?? 0],
          ['label' => "OTHER EXPENSES", 'amount' => optional($allBillings->firstWhere('category','other_expenses'))->total_amount ?? 0],
          ['label' => "INJECTIONS / FLUIDS", 'amount' => optional($allBillings->firstWhere('category','injections_fluids'))->total_amount ?? 0],
        ];
      @endphp

      @php $categoryTotal = 0; @endphp
      @foreach($categories as $i => $cat)
        @php $categoryTotal += $cat['amount']; @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $cat['label'] }}</td>
          <td style="text-align:right;">{{ $cat['amount'] ? number_format($cat['amount'], 2) : '-' }}</td>
        </tr>
      @endforeach

      <tr style="background:#f5f5f5; font-weight:bold;">
        <td colspan="2" style="text-align:right;">Total Additional</td>
        <td style="text-align:right;">₹ {{ number_format($categoryTotal,2) }}</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right;"><b>Grand Total</b></td>
        <td style="text-align:right;"><b>₹ {{ number_format($monthlyTotal + $categoryTotal, 2) }}</b></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right;"><b>Received</b></td>
        <td style="text-align:right;"><b>{{ $totalReceived > 0 ? number_format($totalReceived,2) : '-' }}</b></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right;"><b>Discount</b></td>
        <td style="text-align:right;"><b>{{ number_format($discount ?: ($monthlyTotal + $categoryTotal - $totalReceived),2) }}</b></td>
      </tr>
    </tbody>
  </table>

  <div style="margin-top:40px; font-weight:bold;">Company Seal and Signature</div>
</div>


</div>

<button class="back-btn" onclick="history.back()">⬅ Back</button>
<button class="print-btn" onclick="window.print()">🖨️ Print Summary</button>

</body>
</html>
