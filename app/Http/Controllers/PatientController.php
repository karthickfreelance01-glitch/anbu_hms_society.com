<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientController extends Controller
{
    // 🔹 Show all patients
    public function index()
    {
        $patients = DB::table('patients')->get();
        return view('patient.index', compact('patients'));
    }
    
     public function addmission_form()
    {
        $patients = DB::table('patients')->get();
        return view('patient.admission_form', compact('patients'));
    }

    // 🔹 Show create form
    public function create()
    {
        $lastPatient = DB::table('patients')->orderBy('id', 'desc')->first();
        if ($lastPatient && $lastPatient->ip_number) {
            preg_match('/(\d+)$/', $lastPatient->ip_number, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newRegNo = 'Bri' . ($lastNumber + 1);
        } else {
            $newRegNo = 'Bri1';
        }
        $staff= DB::table('staff')->get();

        return view('patient.create', compact('newRegNo','staff'));
    }

    // 🔹 Store new patient + billing details
    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

       
    //     // 2️⃣ Insert into patients
    //     $patientData = $request->only([
    //         'ip_number', 'date_of_admission', 'time_of_admission',
    //         'name', 'age', 'gender', 'marital_status', 'occupation',
    //         'admitted_by', 'contact_number', 'referral_source',
    //         'present_address', 'permanent_address','blood_group','staff_name'
    //     ]);

    //     if ($request->hasFile('photo')) {
    //         $photo = $request->file('photo');
    //         $filename = time() . '_' . $photo->getClientOriginalName();
    //         $photo->move(public_path('uploads/patient/profile'), $filename);
    //         $patientData['photo'] = 'uploads/patient/profile/' . $filename;
    //     }
    
    //     $patientId = DB::table('patients')->insertGetId($patientData);

    //     // 3️⃣ Billing calculations
    //     $monthly = $request->monthly_amount ?? 0;
    //     $per_day = $request->per_day_amount ?? 0;
    //     $pickUp = $request->pick_up ?? 0;
    //     $medicine = $request->medicine ?? 0;
    //     $other = $request->other_expense ?? 0;
    //     $total = $monthly + $pickUp + $medicine + $other;
    //     $received = $request->received_amount ?? 0;
    //     $pending = $total - $received;

    //     // 4️⃣ Insert into patient_bills (foreign key: patient_id)
    //     DB::table('patient_bills')->insert([
    //         'patient_id' => $patientId,
    //         'monthly_amount' => $monthly,
    //         'pick_up' => $pickUp,
    //         'per_day' =>$per_day,
    //         'medicine' => $medicine,
    //         'other_expense' => $other,
    //         'total_amount' => $total,
    //         'received_amount' => $received,
    //         'payment_mode' => $request->payment_mode,
    //         'payment_mode' => $request->payment_mode,
    //         'payment_mode' => $request->payment_mode,
    //         'account_type' => $request->accounttype,
    //         'payment_date' => now(),
    //         'remarks' => $request->remarks ?? null,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     DB::commit();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Patient and billing details saved successfully!'
    //     ]);
    // }

  public function store(Request $request)
{
    // 1️⃣ Insert into patients table
    $patientData = $request->only([
        'ip_number', 'date_of_admission', 'time_of_admission',
        'name', 'age', 'gender', 'marital_status', 'occupation',
        'admitted_by', 'contact_number', 'referral_source',
        'present_address', 'permanent_address', 'blood_group', 'staff_name'
    ]);

    // Handle photo upload
    if ($request->hasFile('photo')) {
        $photo = $request->file('photo');
        $filename = time() . '_' . $photo->getClientOriginalName();
        $photo->move(public_path('uploads/patient/profile'), $filename);
        $patientData['photo'] = 'uploads/patient/profile/' . $filename;
    }

    // Save patient and get ID
    $patientId = DB::table('patients')->insertGetId($patientData);

    // 2️⃣ Billing calculations
    $monthlyAmount = $request->monthly_amount ?? 0;
    $pickup = $request->pick_up ?? 0;
    $medicine = $request->medicine ?? 0;
    $other = $request->other_expense ?? 0;

    $total = $monthlyAmount + $pickup + $medicine + $other;
    $received = $request->received_amount ?? 0;
    $pending = $total - $received;

    // 3️⃣ Create first payment entry
    $payments = [
        [
            'amount' => $received,
            'date' => $request->date_of_admission,
            'mode' => $request->payment_mode,
            'remarks' => 'Initial Payment'
        ]
    ];

    // 4️⃣ Create monthly bill (from admission date to +1 month)
    $admissionDate = Carbon::parse($request->date_of_admission);
    $nextMonthDate = $admissionDate->copy()->addMonth();
    $per_day = $request->per_day_amount ?? 0;
    $monthlyBills = [
        [
            'from_date' => $admissionDate->format('Y-m-d'),
            'to_date' => $nextMonthDate->format('Y-m-d'),
            'amount' => $monthlyAmount
        ]
    ];

    // 5️⃣ Save bill
    DB::table('patient_bills')->insert([
        'patient_id' => $patientId,
        'monthly_amount' => $monthlyAmount,
        'per_day' => $per_day,       
        'total_amount' => $total,
        'received_amount' => $received,
        'pending_amount' => $pending,
        'payment_mode' => $request->payment_mode,
        'account_type' => $request->accounttype,
        'payments' => json_encode($payments),
        'monthly_bills' => json_encode($monthlyBills),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Patient and first month billing created successfully!'
    ]);
}



// public function store(Request $request)
// {
//     // ✅ Validate inputs
//     $validated = $request->validate( [
//         'reg_no.unique' => 'Register number already exists.',
//         'name.required' => 'Patient name is required.',
        
//     ]);

//     // ✅ Insert patient info
//    $patientData = $request->only([
//         'ip_number', 'date_of_admission', 'time_of_admission',
//         'name', 'age', 'gender', 'marital_status', 'occupation',
//         'admitted_by', 'contact_number', 'referral_source',
//         'present_address', 'permanent_address', 'blood_group', 'staff_name'
//     ]);

//     // Handle photo upload
//     if ($request->hasFile('photo')) {
//         $photo = $request->file('photo');
//         $filename = time() . '_' . $photo->getClientOriginalName();
//         $photo->move(public_path('uploads/patient/profile'), $filename);
//         $patientData['photo'] = 'uploads/patient/profile/' . $filename;
//     }

//     // Insert patient and get ID
//     $patientId = DB::table('patients')->insertGetId($patientData);

//     // ✅ Billing logic (first month)
//     $totalAmount = $request->total_bill;
//     $initialPayment = $request->initial_payment ?? 0;
//     $pendingAmount = $totalAmount - $initialPayment;
//     $billDate = $request->date_of_admission;

      

   

//     // ✅ Prepare initial payment record (stored in next_payments JSON)
//     $paymentRecord = [
//         [
//             'date' => $billDate,
//             'amount' => $initialPayment,
//             'payment_mode' => $request->payment_mode,
//             'hospital_account' => $request->hospital_account ?? null,
//             'note' => 'Initial Payment',
//         ]
//     ];

//     // ✅ Insert main bill
//     $billId = DB::table('patient_bills')->insertGetId([
//         'patient_id' => $patientId,
//         'bill_date' => $request->date_of_admission,
//         'total_amount' => $totalAmount,
//         'initial_payment' => $initialPayment,
//         'pending_amount' => $pendingAmount,
//         'payment_mode' => $request->payment_mode,
       
//         'next_payments' => json_encode($paymentRecord), // ✅ Store initial payment here
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Patient and first-month billing saved successfully!',
//         'patient_id' => $patientId,
//         'bill_id' => $billId,
//     ]);
// }


    // 🔹 Add payment (update billing)
    public function addPayment(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'received_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string|max:50',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Get the current bill record
        $bill = DB::table('patient_bills')->where('patient_id', $request->patient_id)->first();

        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Patient bill not found.',
            ], 404);
        }

        // Prepare new payment entry
        $newPayment = [
            'amount' => $request->received_amount,
            'date' => $request->payment_date,
            'mode' => $request->payment_mode,
            'remarks' => $request->remarks ?? null,
        ];

        // Decode existing payments JSON or initialize empty array
        $payments = $bill->payments ? json_decode($bill->payments, true) : [];

        // Append new payment
        $payments[] = $newPayment;

        // Update the bill: add received_amount, subtract pending_amount, store JSON array
        DB::table('patient_bills')->where('patient_id', $request->patient_id)->update([
            'received_amount' => DB::raw('received_amount + ' . $request->received_amount),
            'pending_amount' => DB::raw('pending_amount - ' . $request->received_amount),
            'payments' => json_encode($payments), // store all payments as JSON
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully!',
            'payments' => $payments, // optional: return full payment history
        ]);
    }

    public function dischargeindex(Request $request)
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $patientsQuery = DB::table('patients')
            ->where('discharge_status', 'Discharged');

        if (!empty($filters['from_date'])) {
            $patientsQuery->whereDate('discharge_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $patientsQuery->whereDate('discharge_date', '<=', $filters['to_date']);
        }

        $patients = $patientsQuery->get();
        return view('patient.dischargeindex', compact('patients'));
    }


public function patientbillreports()
{
    $reports = DB::table('patient_bills')
        ->leftJoin('patients', 'patient_bills.patient_id', '=', 'patients.id')
        ->select(
            'patient_bills.id',
            'patient_bills.patient_id',
            'patient_bills.total_amount',
            'patient_bills.received_amount',
            'patient_bills.payments',
            'patient_bills.monthly_bills',
            'patients.ip_number',
            'patients.name as patient_name',
            'patients.date_of_admission',
            'patients.discharge_status'
        )
        ->orderBy('patient_bills.id', 'desc')
        ->where('patients.discharge_status','=','Pending')
        ->get()
        ->map(function ($bill) {
    $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true) ?: [];
    $payments = json_decode($bill->payments ?? '[]', true) ?: [];

    $admission = \Carbon\Carbon::parse($bill->date_of_admission);
    $discharge = \Carbon\Carbon::parse($bill->date_of_discharge ?? now());

    $monthsStayed = $admission->diffInMonths($discharge);
    $extraDays = $admission->copy()->addMonths($monthsStayed)->diffInDays($discharge);

    // Sort monthly bills by from_date
    usort($monthlyBills, function ($a, $b) {
        return strtotime($a['from_date']) <=> strtotime($b['from_date']);
    });

    $filledBills = [];
    $currentStart = $admission->copy();

    for ($i = 0; $i < $monthsStayed; $i++) {
        $monthNumber = $i + 1;
        $monthStart = $currentStart->copy();
        $monthEnd = $monthStart->copy()->addMonth();

        // 🔥 Determine the correct amount based on last active rate
        $currentAmount = (float)$bill->total_amount;
        foreach ($monthlyBills as $mb) {
            $from = \Carbon\Carbon::parse($mb['from_date']);
            if ($monthStart->gte($from)) {
                $currentAmount = (float)$mb['amount'];
            }
        }

        $filledBills[] = [
            'month_label' => "{$monthNumber}" . match ($monthNumber) {
                1 => 'st Month',
                2 => 'nd Month',
                3 => 'rd Month',
                default => 'th Month'
            },
            'amount' => $currentAmount,
            'period' => $monthStart->format('d-m-Y') . ' to ' . $monthEnd->format('d-m-Y'),
        ];

        $currentStart->addMonth();
    }

    // 🔹 Handle Extra Days with correct last active rate
    if ($extraDays > 0) {
        // Find last valid rate (latest from_date before discharge)
        $lastRate = (float)$bill->total_amount;
        foreach ($monthlyBills as $mb) {
            $from = \Carbon\Carbon::parse($mb['from_date']);
            if ($discharge->gte($from)) {
                $lastRate = (float)$mb['amount'];
            }
        }

        $dailyRate = $lastRate / 30;
        $partialAmount = round($dailyRate * $extraDays, 2);

        $filledBills[] = [
            'month_label' => 'Extra Days',
            'amount' => $partialAmount,
            'period' => $discharge->copy()->subDays($extraDays)->format('d-m-Y') . 
                        ' to ' . $discharge->format('d-m-Y'),
        ];
    }

    // ✅ Totals
    $bill->filled_months = $filledBills;
    $bill->months_stayed = $monthsStayed;
    $bill->extra_days = $extraDays;
    $bill->total_billed = collect($filledBills)->sum('amount');
    $bill->total_paid = collect($payments)->sum(fn($p) => (float)($p['amount'] ?? 0));
    $bill->balance = $bill->total_billed - $bill->total_paid;

    return $bill;
});

    return view('patient.patient_bills', compact('reports'));
}



 
// public function addMonthlyBill(Request $request)
// {
//     $request->validate([
//         'patient_id'     => 'required|integer',
//         'monthly_month'  => 'required|string',
//         'monthly_amount' => 'required|numeric|min:0',
//     ]);

//     $patientId = $request->patient_id;

//     // Get the patient's bill record
//     $bill = DB::table('patient_bills')->where('patient_id', $patientId)->first();
//     if (! $bill) {
//         return response()->json(['success' => false, 'message' => 'Patient bill not found'], 404);
//     }

//     $monthlyBills = is_string($bill->monthly_bills)
//         ? json_decode($bill->monthly_bills, true) ?? []
//         : ($bill->monthly_bills ?? []);

//     $month  = $request->monthly_month;
//     $amount = (float) $request->monthly_amount;

//     $found = false;
//     foreach ($monthlyBills as &$mb) {
//         if (($mb['month'] ?? '') === $month) {
//             $paid = isset($mb['paid']) ? (float)$mb['paid'] : 0;
//             $pending = max(0, $amount - $paid);
//             $mb['amount'] = $amount;
            
//             $found = true;
//             break;
//         }
//     }
//     unset($mb);

//     if (! $found) {
//         $monthlyBills[] = [
//             'month'   => $month,
//             'amount'  => $amount
           
//         ];
//     }

//     DB::table('patient_bills')->where('patient_id', $patientId)->update([
//         'monthly_bills' => json_encode($monthlyBills),
//         'updated_at' => now()
//     ]);

//     return response()->json(['success' => true, 'message' => 'Monthly bill saved']);
// }

public function getMonthlyBills($id)
{
    $patient = DB::table('patient_bills')->where('patient_id', $id)->first();
    return response()->json([
        'monthly_bills' => $patient->monthly_bills ?? '[]'
    ]);
}

public function addMonthlyBill(Request $request)
{
    $request->validate([
        'patient_id' => 'required|integer',
        'monthly_period' => 'required|string',
        'monthly_amount' => 'required|numeric|min:0',
    ]);

    $patientId = $request->patient_id;
    [$fromDate, $toDate] = explode('|', $request->monthly_period);

    $bill = DB::table('patient_bills')->where('patient_id', $patientId)->first();
    $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true);
    
    $monthlyBills[] = [
        'from_date' => $fromDate,
        'to_date' => $toDate,
        'amount' => number_format($request->monthly_amount, 2, '.', '')
    ];

    DB::table('patient_bills')->where('patient_id', $patientId)->update([
        'monthly_bills' => json_encode($monthlyBills, JSON_PRETTY_PRINT),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'New monthly bill added successfully!']);
}

    public function edit($id)
{
    // fetch patient
    $patient = DB::table('patients')->where('id', $id)->first();
    if (!$patient) {
        return redirect()->route('patients.index')->with('error', 'Patient not found.');
    }

    // Fetch the latest bill for this patient.
    // ⚠️ Replace 'bills' with your actual billing table name if different
    $bill = DB::table('patient_bills')
        ->where('patient_id', $id)
        ->orderBy('id', 'desc')   // or orderBy('created_at','desc') if you have timestamps
        ->first();

    /*
      Ensure $bill is an object so Blade property access ($bill->foo) won't error.
      If no bill exists, set $bill to an empty stdClass (properties will be null).
    */
    if (!$bill) {
        $bill = (object) [
            'monthly_amount'   => 0,
            'per_day_amount'   => 0,
            'pick_up'          => 0,
            'total_amount'     => 0,
            'received_amount'  => 0,
            'pending_amount'   => 0,
            'payment_mode'     => null,
            // add any other fields your blade expects to avoid notices
        ];
    }

    return view('patient.edit', compact('patient', 'bill'));
}


public function update(Request $request, $id)
{
    // 1️⃣ Validate request
    $request->validate([
        'ip_number' => 'required|string|max:50',
        'date_of_admission' => 'required|date',
        'time_of_admission' => 'nullable|string|max:20',
        'name' => 'required|string|max:255',
        'age' => 'nullable|numeric|min:0',
        'gender' => 'nullable|string|max:20',
        'marital_status' => 'nullable|string|max:50',
        'occupation' => 'nullable|string|max:255',
        'admitted_by' => 'nullable|string|max:255',
        'contact_number' => 'nullable|string|max:20',
        'referral_source' => 'nullable|string|max:255',
        'present_address' => 'nullable|string|max:500',
        'permanent_address' => 'nullable|string|max:500',
        'monthly_amount' => 'nullable|numeric|min:0',
        'per_day_amount' => 'nullable|numeric|min:0',
        'pick_up' => 'nullable|numeric|min:0',
        'medicine' => 'nullable|numeric|min:0',
        'other_expense' => 'nullable|numeric|min:0',
        'received_amount' => 'nullable|numeric|min:0',
        'payment_mode' => 'nullable|string|max:50',
        'accounttype' => 'nullable|string|max:50',
        'remarks' => 'nullable|string|max:255',
    ]);

    // 2️⃣ Update patient details
    $patientData = $request->only([
        'ip_number', 'date_of_admission', 'time_of_admission',
        'name', 'age', 'gender', 'marital_status', 'occupation',
        'admitted_by', 'contact_number', 'referral_source',
        'present_address', 'permanent_address'
    ]);

    // Handle photo upload if new file given
    if ($request->hasFile('photo')) {
        $photo = $request->file('photo');
        $filename = time() . '_' . $photo->getClientOriginalName();
        $photo->move(public_path('uploads/patient/profile'), $filename);
        $patientData['photo'] = 'uploads/patient/profile/' . $filename;
    }

    DB::table('patients')->where('id', $id)->update($patientData);

    // 3️⃣ Billing calculations
    $monthlyAmount = $request->monthly_amount ?? 0;
    $per_day = $request->per_day_amount ?? 0;
    $pickup = $request->pick_up ?? 0;
    $medicine = $request->medicine ?? 0;
    $other = $request->other_expense ?? 0;

    $total = $monthlyAmount + $pickup + $medicine + $other;
    $received = $request->received_amount ?? 0;
    $pending = $total - $received;

    // 4️⃣ Generate updated monthly bill period
    $admissionDate = Carbon::parse($request->date_of_admission);
    $nextMonthDate = $admissionDate->copy()->addMonth();

    $monthlyBills = [
        [
            'from_date' => $admissionDate->format('Y-m-d'),
            'to_date' => $nextMonthDate->format('Y-m-d'),
            'amount' => $monthlyAmount
        ]
    ];

    // 5️⃣ Check if patient bill exists
    $billExists = DB::table('patient_bills')->where('patient_id', $id)->exists();

    if ($billExists) {
        // 🔹 Update existing billing record
        DB::table('patient_bills')->where('patient_id', $id)->update([
            'monthly_amount' => $monthlyAmount,
            'per_day' => $per_day,
            'pick_up' => $pickup,
            'medicine' => $medicine,
            'other_expense' => $other,
            'total_amount' => $total,
            'received_amount' => $received,
            'pending_amount' => $pending,
            'payment_mode' => $request->payment_mode,
            'account_type' => $request->accounttype,
            'monthly_bills' => json_encode($monthlyBills),
            'payment_date' => now(),
            'remarks' => $request->remarks ?? null,
            'updated_at' => now(),
        ]);
    } else {
        // 🔹 Create new billing record
        DB::table('patient_bills')->insert([
            'patient_id' => $id,
            'monthly_amount' => $monthlyAmount,
            'per_day' => $per_day,
            'pick_up' => $pickup,
            'medicine' => $medicine,
            'other_expense' => $other,
            'total_amount' => $total,
            'received_amount' => $received,
            'pending_amount' => $pending,
            'payment_mode' => $request->payment_mode,
            'account_type' => $request->accounttype,
            'monthly_bills' => json_encode($monthlyBills),
            'payment_date' => now(),
            'remarks' => $request->remarks ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // 6️⃣ Return success response
    return response()->json([
        'success' => true,
        'message' => 'Patient and billing details updated successfully!',
    ]);
}


    // 🔹 Delete Patient
    public function destroy($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        if (!$patient) {
            return response()->json(['error' => 'Patient not found.'], 404);
        }

        DB::table('patients')->where('id', $id)->delete();
        DB::table('patient_bills')->where('patient_id', $id)->delete();

        return response()->json(['success' => 'Patient deleted successfully!']);
    }

    // 🔹 View Patient Details
  public function show($id)
{
    $patient = DB::table('patients')
        ->leftJoin('patient_bills', 'patient_bills.patient_id', '=', 'patients.id')
        ->where('patients.id', $id)
        ->select('patients.*', 'patient_bills.monthly_amount', 'patient_bills.received_amount',
        'patient_bills.pending_amount', 'patient_bills.payment_mode', 'patient_bills.account_type')
        ->first();

    if (!$patient) {
        return redirect()->route('patients.index')->with('error', 'Patient not found.');
    }

    return view('patient.view', compact('patient'));
}


    /// Discharge

    public function reports()
    {
        $patients = DB::table('patients')
            ->select('id', 'ip_number', 'name', 'contact_number', 'gender', 'date_of_admission')
            ->where('discharge_status','Pending')
            ->orderBy('id', 'desc')
            ->get();

        return view('patient.reports', compact('patients'));
    }

    // 🔹 Get full discharge/billing data (used for discharge bill view or AJAX)
    public function getPatientDetails($patientId)
    {
        // 1️⃣ Get patient details
        $patient = DB::table('patients')->where('id', $patientId)->first();
        if (!$patient) {
            return response()->json(['error' => 'Patient not found']);
        }

        // 2️⃣ Get bill details
        $bill = DB::table('patient_bills')
            ->where('patient_id', $patientId)
            ->select(
                DB::raw('SUM(monthly_amount) as monthly_amount'),
                DB::raw('SUM(per_day) as per_day'),
                DB::raw('SUM(received_amount) as received_amount'),
                DB::raw('SUM(pick_up) as pickup_amount')
            )
            ->first();

        $monthlyAmount  = $bill->monthly_amount ?? 0;
        $perDayAmount   = $bill->per_day ?? 0;
        $receivedAmount = $bill->received_amount ?? 0;
        $pickupAmount   = $bill->pickup_amount ?? 0;

        // 3️⃣ Admission & discharge dates
        $admissionDate = $patient->date_of_admission ? Carbon::parse($patient->date_of_admission) : Carbon::now();
        $dischargeDate = Carbon::now();

        $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
        $daysStayed   = $admissionDate->diffInDays($dischargeDate);

        // 4️⃣ Extra days beyond full months
        $extraDays = $daysStayed - ($monthsStayed * 30);
        if ($extraDays < 0) $extraDays = 0;

        // 5️⃣ Calculate totals
        $monthlyTotalAmount = $monthlyAmount * $monthsStayed;
        $extraDaysAmount = $perDayAmount * $extraDays;
        $monthlyTotalPending = $monthlyTotalAmount - $receivedAmount;

        // 6️⃣ Category-wise billing
        $medicalBillings = DB::table('patient_billings')
            ->where('patient_id', $patientId)
            ->select(
                'category',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(received_amount) as total_received'),
                DB::raw('SUM(pending_amount) as total_pending')
            )
            ->groupBy('category')
            ->get();
          //  dd( $medicalBillings);

        // 7️⃣ Add custom entries
        $extraEntries = collect([
            (object)[
                'category' => 'Monthly Charges',
                'total_amount' => $monthlyTotalAmount,
                'total_received' => $receivedAmount,
                'total_pending' => $monthlyTotalPending,
            ],
            (object)[
                'category' => 'Extra Days Charges',
                'total_amount' => $extraDaysAmount,
                'total_received' => 0,
                'total_pending' => $extraDaysAmount,
            ],
            
        ]);

        $allBillings = $medicalBillings->merge($extraEntries);

        // 8️⃣ Totals
        $totalAmount = $allBillings->sum('total_amount');
        $totalReceived = $allBillings->sum('total_received');
        $totalPending = $allBillings->sum('total_pending');

        // 9️⃣ ✅ Return all required data for JS
        return response()->json([
            'patient' => $patient,
            'admissionDate' => $admissionDate->format('d-m-Y'),
            'dischargeDate' => $dischargeDate->format('d-m-Y'),
            'monthsStayed' => $monthsStayed,
            'extraDays' => $extraDays,
            'monthlyAmount' => $monthlyAmount,        // ✅ added
            'perDayAmount' => $perDayAmount,          // ✅ added
            'monthlyTotalAmount' => $monthlyTotalAmount,
            'extraDaysAmount' => $extraDaysAmount,
            'receivedAmount' => $receivedAmount,
            'pickupAmount' => $pickupAmount,
            'monthlyTotalPending' => $monthlyTotalPending,
            'allBillings' => $allBillings,
            'totalAmount' => $totalAmount,
            'totalReceived' => $totalReceived,
            'totalPending' => $totalPending,
        ]);
    }

public function dischargeSelect()
{
    $patients = DB::table('patients')
        ->where('discharge_status','Pending')
        ->select('id', 'ip_number', 'name')
        ->get();

    return view('patient.discharge_select', compact('patients'));
}

// public function dischargePdf($id)
// {
//     // 1️⃣ Get patient details
//     $patient = DB::table('patients')->where('id', $id)->first();
//     if (!$patient) {
//         return redirect()->back()->with('error', 'Patient not found.');
//     }

//     // 2️⃣ Get billing base data
//     $bill = DB::table('patient_bills')
//         ->where('patient_id', $id)
//         ->select(
//             DB::raw('SUM(monthly_amount) as monthly_amount'),
//             DB::raw('SUM(per_day) as per_day'),
//             DB::raw('SUM(received_amount) as received_amount'),
//             DB::raw('SUM(pick_up) as pickup_amount')
//         )
//         ->first();

//     $monthlyAmount  = $bill->monthly_amount ?? 0;
//     $perDayAmount   = $bill->per_day ?? 0;
//     $receivedAmount = $bill->received_amount ?? 0;
//     $pickupAmount   = $bill->pickup_amount ?? 0;

//     // 3️⃣ Admission & Discharge Dates
//     $admissionDate = Carbon::parse($patient->date_of_admission);
//     $dischargeDate = Carbon::now();

//     // 4️⃣ Duration Calculations
//     $daysStayed   = $admissionDate->diffInDays($dischargeDate);
//     $monthsStayed = floor($daysStayed / 30);
//     $extraDays    = $daysStayed % 30;

//     // 5️⃣ Calculate Stay Charges
//     $monthlyTotalAmount = $monthsStayed * $monthlyAmount;
//     $extraDaysAmount    = $extraDays * $perDayAmount;
//     $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
//     $combinedPending = max($combinedTotalAmount - $receivedAmount, 0);

//     // 6️⃣ Get Other Category-Wise Billing
//     $medicalBillings = DB::table('patient_billings')
//         ->where('patient_id', $id)
//         ->select(
//             'category',
//             DB::raw('SUM(amount) as total_amount'),
//             DB::raw('SUM(received_amount) as total_received'),
//             DB::raw('SUM(pending_amount) as total_pending')
//         )
//         ->groupBy('category')
//         ->get();

//     // 7️⃣ Add Room / Stay Charges Row
//     $extraEntries = collect([
//         (object)[
//             'category' => 'Monthly Stayed',
//             'total_amount' => $combinedTotalAmount,
//             'total_received' => $receivedAmount,
//             'total_pending' => $combinedPending,
//         ]
//     ]);

//     $allBillings = $extraEntries->merge($medicalBillings);

//     // 8️⃣ Overall Totals
//     $totalAmount   = $allBillings->sum('total_amount');
//     $totalReceived = $allBillings->sum('total_received');
//     $totalPending  = $allBillings->sum('total_pending');

//     // 9️⃣ Generate PDF
//     $pdf = Pdf::loadView('discharge_bill', compact(
//         'patient',
//         'admissionDate',
//         'dischargeDate',
//         'monthsStayed',
//         'monthlyAmount',
//         'extraDays',
//         'bill',
//         'combinedTotalAmount',
//         'combinedPending',
//         'receivedAmount',
//         'pickupAmount',
//         'allBillings',
//         'totalAmount',
//         'totalReceived',
//         'totalPending'
//     ));

//     $fileName = 'Discharge_Bill_' . str_replace(' ', '_', $patient->name) . '.pdf';
//     return $pdf->stream($fileName);
// }


public function dischargePdf($id)
{
    // 1️⃣ Get patient
    $patient = DB::table('patients')->where('id', $id)->first();
    if (!$patient) {
        return redirect()->back()->with('error', 'Patient not found.');
    }

    // 2️⃣ Get monthly fees + received amount
    $bill = DB::table('patient_bills')
        ->where('patient_id', $id)
        ->select(
            DB::raw('SUM(monthly_amount) as monthly_amount'),
            DB::raw('SUM(received_amount) as received_amount')
        )
        ->first();

    $monthlyAmount  = floatval($bill->monthly_amount ?? 0);  // Example: 12000
    $receivedAmount = floatval($bill->received_amount ?? 0); // Example: 5000

    // 3️⃣ Get discharge date (session override or DB)
    $sessionDate = session('discharge_date');
    $dischargeDate = $sessionDate
        ? Carbon::parse($sessionDate)->startOfDay()
        : Carbon::parse($patient->discharge_date ?? Carbon::today())->startOfDay();

    // 4️⃣ Admission date
    $admissionDate = Carbon::parse($patient->date_of_admission)->startOfDay();

    // 5️⃣ Total days stayed
    $totalDaysStay = $admissionDate->diffInDays($dischargeDate);

    // 6️⃣ Billable full months (ONLY count 30-day completed months)
    $monthsToBill = intdiv($totalDaysStay, 30);

    // 7️⃣ Extra days (remaining days)
    $extraDays = $totalDaysStay % 30;

    // 8️⃣ Extra days pro-rated amount
    $extraDayAmount = 0;
    if ($extraDays > 0) {
        $extraDayAmount = ($extraDays * $monthlyAmount) / 30;
    }

    // 9️⃣ Final payable amount (months + extra days)
    $monthlyTotal = ($monthsToBill * $monthlyAmount) + $extraDayAmount;

    // 🔟 Pending / Refund logic
    $pendingAmount = 0;
    $refundAmount  = 0;

    if ($receivedAmount > $monthlyTotal) {
        $refundAmount = $receivedAmount - $monthlyTotal;
    } else {
        $pendingAmount = $monthlyTotal - $receivedAmount;
    }

    // 1️⃣1️⃣ Other medical/service categories
    $otherBillings = DB::table('patient_billings')
        ->where('patient_id', $id)
        ->select(
            'category',
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(received_amount) as total_received'),
            DB::raw('SUM(pending_amount) as total_pending')
        )
        ->groupBy('category')
        ->get();

    // 1️⃣2️⃣ Prepare billing rows for PDF
    $billingRows = collect([]);

    if ($monthsToBill > 0) {
        $billingRows->push((object)[
            'category'       => "Monthly Charges ({$monthsToBill} × ₹{$monthlyAmount})",
            'total_amount'   => $monthsToBill * $monthlyAmount,
            'total_received' => $receivedAmount,
            'total_pending'  => 0
        ]);
    }

    if ($extraDays > 0) {
        $billingRows->push((object)[
            'category'       => "Extra Days ({$extraDays} days × ₹{$monthlyAmount} / 30)",
            'total_amount'   => round($extraDayAmount, 2),
            'total_received' => $receivedAmount,
            'total_pending'  => 0
        ]);
    }

    if ($pendingAmount > 0) {
        $billingRows->push((object)[
            'category'       => "Pending Amount",
            'total_amount'   => 0,
            'total_received' => 0,
            'total_pending'  => round($pendingAmount, 2)
        ]);
    }

    if ($refundAmount > 0) {
        $billingRows->push((object)[
            'category'       => "Refund Amount",
            'total_amount'   => 0,
            'total_received' => 0,
            'total_pending'  => 0,
            'refund'         => round($refundAmount, 2)
        ]);
    }

    $allBillings = $billingRows->merge($otherBillings);

    // 1️⃣3️⃣ Totals
    $totalAmount   = $monthlyTotal + $otherBillings->sum('total_amount');
    $totalReceived = $receivedAmount + $otherBillings->sum('total_received');
    $totalPending  = $pendingAmount + $otherBillings->sum('total_pending');

    // 1️⃣4️⃣ Generate PDF
    $pdf = Pdf::loadView('discharge_bill', compact(
        'patient',
        'admissionDate',
        'dischargeDate',
        'monthlyAmount',
        'extraDays',
        'extraDayAmount',
        'allBillings',
        'totalAmount',
        'totalReceived',
        'totalPending',
        'refundAmount'
    ))->setPaper('a4');

    return $pdf->stream('Discharge_Bill_' . str_replace(' ', '_', $patient->name) . '.pdf');
}







public function updateMonthlyBill(Request $request)
{
    $request->validate([
        'patient_id' => 'required|integer',
        'index' => 'required|integer',
        'amount' => 'required|numeric|min:0',
    ]);

    $bill = DB::table('patient_bills')->where('patient_id', $request->patient_id)->first();
    if (!$bill) return response()->json(['message' => 'Patient not found'], 404);

    $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true);

    if (!isset($monthlyBills[$request->index])) {
        return response()->json(['message' => 'Invalid bill index'], 400);
    }

    $monthlyBills[$request->index]['amount'] = number_format($request->amount, 2, '.', '');

    DB::table('patient_bills')->where('patient_id', $request->patient_id)->update([
        'monthly_bills' => json_encode($monthlyBills, JSON_PRETTY_PRINT),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Monthly bill updated successfully!']);
}

public function deleteMonthlyBill(Request $request)
{
    $request->validate([
        'patient_id' => 'required|integer',
        'index' => 'required|integer',
    ]);

    $bill = DB::table('patient_bills')->where('patient_id', $request->patient_id)->first();
    if (!$bill) return response()->json(['message' => 'Patient not found'], 404);

    $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true);
    if (!isset($monthlyBills[$request->index])) {
        return response()->json(['message' => 'Invalid bill index'], 400);
    }

    array_splice($monthlyBills, $request->index, 1);

    DB::table('patient_bills')->where('patient_id', $request->patient_id)->update([
        'monthly_bills' => json_encode($monthlyBills, JSON_PRETTY_PRINT),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Monthly bill deleted successfully!']);
}

public function getPayment($ip_number, $index)
{
    $bill = DB::table('patient_bills')
        ->join('patients', 'patient_bills.patient_id', '=', 'patients.id')
        ->where('patients.ip_number', $ip_number)
        ->select('patient_bills.payments')
        ->first();

    $payments = json_decode($bill->payments ?? '[]', true);
    return response()->json($payments[$index] ?? []);
}

public function updatePayment(Request $req)
{
    // Fetch patient bill using IP number
    $bill = DB::table('patient_bills')
        ->join('patients', 'patient_bills.patient_id', '=', 'patients.id')
        ->where('patients.ip_number', $req->ip_number)
        ->select('patient_bills.id', 'patient_bills.payments')
        ->first();

    if (!$bill) {
        return response()->json(['message' => 'Bill not found'], 404);
    }

    // Decode existing payments
    $payments = json_decode($bill->payments ?? '[]', true);

    // Update specific payment index
    $payments[$req->payment_index] = [
        'date' => $req->date,
        'amount' => $req->amount,
        'mode' => $req->mode,
        'remarks' => $req->remarks,
    ];

    // Recalculate received amount (sum of all payment amounts)
    $receivedAmount = array_sum(array_column($payments, 'amount'));

    // Update database
    DB::table('patient_bills')
        ->where('id', $bill->id)
        ->update([
            'payments' => json_encode($payments),
            'received_amount' => $receivedAmount,
        ]);

    return response()->json([
        'message' => 'Payment updated successfully.',
        'received_amount' => $receivedAmount
    ]);
}

public function deletePayment(Request $req)
{
    $bill = DB::table('patient_bills')
        ->join('patients', 'patient_bills.patient_id', '=', 'patients.id')
        ->where('patients.ip_number', $req->ip_number)
        ->select('patient_bills.id', 'patient_bills.payments')
        ->first();

    $payments = json_decode($bill->payments ?? '[]', true);
    array_splice($payments, $req->index, 1);

    DB::table('patient_bills')->where('id', $bill->id)
        ->update(['payments' => json_encode($payments)]);

    return response()->json(['message' => 'Payment deleted successfully.']);
}



}
