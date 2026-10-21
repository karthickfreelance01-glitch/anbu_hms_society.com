<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Patient Discharge Summary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    @page { size: A4; margin: 18mm; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      color: #111;
      margin: 0;
      -webkit-print-color-adjust: exact;
      background: #fff;
    }

    .container { width: 100%; max-width: 900px; margin: 0 auto; }

    /* ===== HEADER ===== */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #ff0909;
      padding-bottom: 6px;
      margin-bottom: 10px;
    }

    .header img {
      width: 65px;
      height: auto;
    }

    .header-center {
      text-align: center;
      flex-grow: 1;
    }

    .hospital-name {
      color: #ff0909;
      font-size: 20px;
      font-weight: 800;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }

    .hospital-sub {
      color: #ff0909;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .hospital-address {
      color: #000;
      font-size: 12px;
      margin-top: 3px;
      line-height: 1.4;
    }

    /* ===== TITLES ===== */
    .doc-title {
      text-align: center;
      color: #ff0909;
      font-weight: 700;
      font-size: 17px;
      margin: 16px 0 14px;
      text-decoration: underline;
      letter-spacing: 0.3px;
    }

    /* ===== TABLES ===== */
    table.std {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      border-bottom: 2px solid #ff0909;
    }

    table.std td, table.std th {
      padding: 6px 8px;
      vertical-align: top;
      font-size: 14px;
    }

    table.std th {
      text-align: left;
      width: 35%;
      font-weight:700;
      color:#333;
    }

    .line { border-top:1px solid #000; margin: 18px 0; }
    .muted { color:#555; font-size:13px; }
    .small { font-size:13px; color:#333; }

    /* ===== SIGNATURES ===== */
    .signature {
      margin-top: 36px;
      display:flex;
      justify-content:space-between;
    }

    .sig-block { width: 40%; text-align:center; }

    .sig-line {
      border-top:1px solid #000;
      margin: 40px auto 6px;
      width:80%;
    }

    /* ===== ADVICE BOX ===== */
    .advice-box {
      border-radius: 8px;
      padding: 18px 20px;
      min-height: 380px;
    }

    /* ===== PAGE STRUCTURE ===== */
    .page { page-break-after: always; padding-bottom: 10px; }
    .no-break { page-break-inside: avoid; }

    /* ===== PRINT BUTTON ===== */
    .print-btn {
      position: fixed;
      right: 30px;
      bottom: 30px;
      z-index: 9999;
      background: linear-gradient(90deg, #ff0909, #ff5f5f);
      color: white;
      border: none;
      border-radius: 30px;
      padding: 14px 24px;
      font-weight: bold;
      font-size: 15px;
      box-shadow: 0 6px 15px rgba(255,0,0,0.3);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .print-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(255,0,0,0.4);
    }

    @media print { 
      .print-btn, .back-btn { display:none; } 
    }

    /* ===== BACK BUTTON ===== */
    .back-btn {
      display: inline-block;
      margin: 12px 0;
      padding: 8px 14px;
      background: linear-gradient(90deg, #ff0909, #ff5f5f);
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 700;
      box-shadow: 0 4px 10px rgba(255,0,0,0.25);
    }

    .back-btn:hover {
      background: linear-gradient(90deg, #e00000, #ff4d4d);
    }

  </style>
</head>

<body>
  <div style="margin:12px 0;">
    <a href="{{ url('patients') }}" class="back-btn">&larr; Back to Patients</a>
  </div>

  <div class="container">

    {{-- ================== PAGE 1: DISCHARGE FORM ================== --}}
    <div class="page">
      <div class="header">
        <img src="{{ asset('assets/img/IMG_1774.png') }}" alt="Left Logo" style="width:12% !important;">
        <div class="header-center">
          <div class="hospital-name">THE MAATRAM REHABILITATION CENTER</div>
          <div class="hospital-sub">& DE ADDICTION FOR DRUGS</div>
          <div class="hospital-address">
            Govt. Regt No : 00529<br>
            SF. No : 332/5, Nehru Nagar West, Kalapatti Main Road, Sitra, Coimbatore – 641014<br>
            Ph : 94437 95265 / 97901 33349 / 98650 81843
          </div>
        </div>
        <img src="{{ asset('assets/img/IMG_1775.png') }}" alt="Right Logo" style="width:12% !important;">
      </div>

      <div class="doc-title">DISCHARGE FORM</div>

      <table class="std">
        <tr><th>Name of the Patient</th><td>{{ $patient->name ?? '-' }}</td></tr>
        <tr><th>Age / Gender</th><td>{{ $patient->age ?? '-' }} / {{ $patient->gender ?? '-' }}</td></tr>
        <tr><th>IP Registration No</th><td>{{ $patient->ip_number ?? '-' }}</td></tr>
        <tr>
          <th>Treatment Period</th>
          <td>From: {{ $admissionDate->format('d-m-Y') }} &nbsp;&nbsp;&nbsp; To: {{ $dischargeDate->format('d-m-Y') }}</td>
        </tr>
        <tr><th>Address</th><td>{{ $patient->address ?? '-' }}</td></tr>
        <tr><th>Phone no</th><td>{{ $patient->contact_number ?? '-' }}</td></tr>
        <tr><th>Guardian Name</th><td>{{ $patient->guardian_name ?? '-' }}</td></tr>
      </table>

      <div class="line"></div>

      <div class="muted small">
        <strong style="color:#ff0909;">FOLLOW UP AGREEMENT</strong><br><br>
        I/We hereby agree to come for follow up at THE MAATRAM REHABILITATION CENTRE for counselling and medications,
        to maintain patient’s recovery as advised by the centre professionals. I/We agree that the patient is in good condition
        at the time of discharge and I/We will take full responsibility for his health and behaviour thereafter and will not
        hold THE MAATRAM REHABILITATION CENTRE management or staff responsible for any future issues.
      </div>

      <div class="signature">
        <div class="sig-block">
          <div class="sig-line"></div>
          <div class="small">Patient Signature</div>
        </div>
        <div class="sig-block">
          <div class="sig-line"></div>
          <div class="small">Guardian Signature</div>
        </div>
      </div>
    </div>

    {{-- ================== PAGE 2: DISCHARGE ADVICE ================== --}}
    <div class="page">
      <div class="header">
        <img src="{{ asset('assets/img/IMG_1774.png') }}" alt="Left Logo" style="width:12% !important;">
        <div class="header-center">
          <div class="hospital-name">THE MAATRAM REHABILITATION CENTER</div>
          <div class="hospital-sub">& DE ADDICTION FOR DRUGS</div>
          <div class="hospital-address">
            SF. No : 332/5, Nehru Nagar West, Kalapatti Main Road, Sitra, Coimbatore – 641014
          </div>
        </div>
        <img src="{{ asset('assets/img/IMG_1775.png') }}" alt="Right Logo" style="width:12% !important;">
      </div>

      <div class="doc-title">DISCHARGE ADVICE</div>

      <div class="advice-box">
        <h4 style="color:#ff0909; font-weight:700; text-transform:uppercase; font-size:15px; margin-bottom:12px;">
          Advice & Follow-up Instructions
        </h4>

        <div style="font-size:15px; color:#111; line-height:1.8; text-align:justify;">
          {{ $patient->discharge_advice ?? 
          "I/We hereby agree to come for follow up at THE MAATRAM REHABILITATION CENTRE for counselling and medications,
          to maintain patient’s recovery as advised by the centre professionals. I/We agree that patient is in good condition
          at the time of discharge and I/We will take full responsibility if any issue arises with his health and behaviour
          in the upcoming days and will not blame THE MAATRAM REHABILITATION CENTRE management or staff." }}
        </div>
      </div>

      <div class="line"></div>

      <div style="display:flex; justify-content:space-between; margin-top:40px;">
        <div style="width:45%; text-align:left;">
          <div class="sig-line"></div>
          <div class="small">Counselor / Administrator</div>
        </div>
        <div style="width:45%; text-align:right;">
          <div class="sig-line" style="float:right;"></div>
          <div class="small">Doctor / Consultant</div>
        </div>
      </div>
    </div>

    {{-- ================== PAGE 3: SUMMARY BILL ================== --}}
    <div class="page">
      <div class="header">
        <img src="{{ asset('assets/img/IMG_1774.png') }}" alt="Left Logo" style="width:12% !important;">
        <div class="header-center">
          <div class="hospital-name">THE MAATRAM REHABILITATION CENTER</div>
          <div class="hospital-sub">& DE ADDICTION FOR DRUGS</div>
          <div class="hospital-address">
            SF. No : 332/5, Nehru Nagar West, Kalapatti Main Road, Sitra, Coimbatore – 641014
          </div>
        </div>
        <img src="{{ asset('assets/img/IMG_1775.png') }}" alt="Right Logo" style="width:12% !important;">
      </div>

      <div class="doc-title">PATIENT SUMMARY DETAILS / SUMMARY CASH BILL</div>

      {{-- Billing Table --}}
      <table class="std">
        <tr>
          <th>Bill Number</th><td>{{ $bill->bill_number ?? $bill->id ?? '-' }}</td>
          <th>Admission Date</th><td>{{ $admissionDate->format('d-m-Y') }}</td>
        </tr>
        <tr>
          <th>IP Number</th><td>{{ $patient->ip_number ?? '-' }}</td>
          <th>Discharge Date</th><td>{{ $dischargeDate->format('d-m-Y') }}</td>
        </tr>
        <tr>
          <th>Patient Name</th><td>{{ $patient->name }}</td>
          <th>Age / Sex</th><td>{{ $patient->age ?? '-' }} / {{ $patient->gender ?? '-' }}</td>
        </tr>
        <tr>
          <th>Address</th><td>{{ $patient->address ?? '-' }}</td>
          <th>Contact No</th><td>{{ $patient->contact_number ?? '-' }}</td>
        </tr>
        <tr>
          <th>Patient Case</th><td colspan="3">{{ $patient->case_description ?? '-' }}</td>
        </tr>
      </table>

      {{-- Billing Items --}}
      <table style="width:100%; border-collapse:collapse; font-size:15px; line-height:1.6;">
        <tbody>
          @php
            $categories = [
                ['label' => "MONTHLY ($monthlyAmount) * $monthsStayed", 'amount' => $monthlyTotalAmount],
                ['label' => "EXTRA DAYS", 'amount' => $extraDays > 0 ? $extraDaysAmount : 0],
                ['label' => "MEDICINES", 'amount' => optional($allBillings->firstWhere('category','medicines'))->total_amount ?? 0],
                ['label' => "PATIENT PICK CHARGES", 'amount' => $pickupAmount],
                ['label' => "BLOOD TEST", 'amount' => optional($allBillings->firstWhere('category','blood_test'))->total_amount ?? 0],
                ['label' => "SCAN", 'amount' => optional($allBillings->firstWhere('category','scan'))->total_amount ?? 0],
                ['label' => "OTHER EXPENSES", 'amount' => optional($allBillings->firstWhere('category','other_expenses'))->total_amount ?? 0],
                ['label' => "INJECTIONS / FLUIDS", 'amount' => optional($allBillings->firstWhere('category','injections_fluids'))->total_amount ?? 0],
            ];
          @endphp

          @foreach($categories as $index => $cat)
            <tr>
              <td style="width:70%; padding:5px 0;">{{ $index+1 }}. {{ $cat['label'] }}</td>
              <td style="width:5%;">=</td>
              <td style="width:25%; text-align:right;">{{ $cat['amount'] == 0 ? '-' : number_format($cat['amount'], 0) }}</td>
            </tr>
          @endforeach

          <tr><td colspan="3"><hr style="border-top:1px solid #000;"></td></tr>
          <tr>
            <td style="text-align:right; font-weight:bold;">TOTAL</td>
            <td>=</td>
            <td style="text-align:right;">{{ number_format($totalAmount, 0) }}</td>
          </tr>
          <tr>
            <td style="text-align:right; font-weight:bold;">RECEIVED</td>
            <td>=</td>
            <td style="text-align:right;">{{ $totalReceived > 0 ? number_format($totalReceived, 0) : '-' }}</td>
          </tr>
          <tr>
            <td style="text-align:right; font-weight:bold;">DISCOUNT</td>
            <td>=</td>
            <td style="text-align:right;">{{ $discount > 0 ? number_format($discount, 0) : '-' }}</td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top:24px; text-align:right;">
        <div class="small">COMPANY SEAL AND SIGN</div>
        <div class="sig-line" style="width:30%; margin-left:auto; margin-top:12px;"></div>
      </div>
    </div>
  </div>

  <button class="print-btn" onclick="window.print()">🖨️ Print Summary</button>

</body>
</html>
