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

        return view('patient.create', compact('newRegNo'));
    }

    // 🔹 Store new patient + billing details
    public function store(Request $request)
    {
        DB::beginTransaction();

       
        // 2️⃣ Insert into patients
        $patientData = $request->only([
            'ip_number', 'date_of_admission', 'time_of_admission',
            'name', 'age', 'gender', 'marital_status', 'occupation',
            'admitted_by', 'contact_number', 'referral_source',
            'present_address', 'permanent_address','patient_case'
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('patient_photos', 'public');
            $patientData['photo'] = $photo;
        }

        $patientId = DB::table('patients')->insertGetId($patientData);

        // 3️⃣ Billing calculations
        $monthly = $request->monthly_amount ?? 0;
        $per_day = $request->per_day_amount ?? 0;
        $pickUp = $request->pick_up ?? 0;
        $medicine = $request->medicine ?? 0;
        $other = $request->other_expense ?? 0;
        $total = $monthly + $pickUp + $medicine + $other;
        $received = $request->received_amount ?? 0;
        $pending = $total - $received;

        // 4️⃣ Insert into patient_bills (foreign key: patient_id)
        DB::table('patient_bills')->insert([
            'patient_id' => $patientId,
            'monthly_amount' => $monthly,
            'pick_up' => $pickUp,
            'per_day' =>$per_day,
            'medicine' => $medicine,
            'other_expense' => $other,
            'total_amount' => $total,
            'received_amount' => $received,
            'payment_mode' => $request->payment_mode,
            'account_type' => $request->accounttype,
            'payment_date' => now(),
            'remarks' => $request->remarks ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Patient and billing details saved successfully!'
        ]);
    }

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

    public function dischargeindex()
    {
        $patients = DB::table('patients')
        ->where('discharge_status','Discharged')->get();
        return view('patient.dischargeindex', compact('patients'));
    }

    // 🔹 Generate Patient Billing Report
  public function patientbillreports()
    {
        $reports = DB::table('patient_bills')
            ->leftJoin('patients', 'patient_bills.patient_id', '=', 'patients.id')
            ->select(
                'patients.id as patient_id',
                'patients.ip_number',
                'patients.name',
                'patients.contact_number',
                'patients.date_of_admission',
                'patients.time_of_admission',
                'patient_bills.monthly_amount',
                'patient_bills.received_amount',
                'patient_bills.payment_mode',
                'patient_bills.payments', // JSON column for storing all payments
                DB::raw('(patient_bills.monthly_amount - patient_bills.received_amount) as pending_amount')
            )
            ->where('patients.discharge_status','Pending')
            ->orderBy('patients.date_of_admission', 'desc')
            ->get();

        return view('patient.patient_bills', compact('reports'));
    }


    // 🔹 Edit Patient
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
    

    // 2️⃣ Update patient details
    $patientData = $request->only([
        'ip_number', 'date_of_admission', 'time_of_admission',
        'name', 'age', 'gender', 'marital_status', 'occupation',
        'admitted_by', 'contact_number', 'referral_source',
        'present_address', 'permanent_address'
    ]);

    if ($request->hasFile('photo')) {
        $photo = $request->file('photo')->store('patient_photos', 'public');
        $patientData['photo'] = $photo;
    }

    DB::table('patients')->where('id', $id)->update($patientData);

    // 3️⃣ Billing calculations
    $monthly = $request->monthly_amount ?? 0;
    $per_day = $request->per_day_amount ?? 0;
    $pickUp = $request->pick_up ?? 0;
    $medicine = $request->medicine ?? 0;
    $other = $request->other_expense ?? 0;
    $total = $monthly + $pickUp + $medicine + $other;
    $received = $request->received_amount ?? 0;
    $pending = $total - $received;

    // 4️⃣ Update or insert into patient_bills
    $billExists = DB::table('patient_bills')->where('patient_id', $id)->exists();

    if ($billExists) {
        // Update existing record
        DB::table('patient_bills')->where('patient_id', $id)->update([
            'monthly_amount' => $monthly,
            'pick_up' => $pickUp,
            'per_day' => $per_day,
            'medicine' => $medicine,
            'other_expense' => $other,
            'total_amount' => $total,
            'received_amount' => $received,
            'pending_amount' => $pending,
            'payment_mode' => $request->payment_mode,
            'account_type' => $request->accounttype,
            'payment_date' => now(),
            'remarks' => $request->remarks ?? null,
            'updated_at' => now(),
        ]);
    } else {
        // Insert new record if not found
        DB::table('patient_bills')->insert([
            'patient_id' => $id,
            'monthly_amount' => $monthly,
            'pick_up' => $pickUp,
            'per_day' => $per_day,
            'medicine' => $medicine,
            'other_expense' => $other,
            'total_amount' => $total,
            'received_amount' => $received,
            'pending_amount' => $pending,
            'payment_mode' => $request->payment_mode,
            'payment_date' => now(),
            'remarks' => $request->remarks ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // 5️⃣ Return response
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
        $patient = DB::table('patients')->where('id', $id)->first();
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

public function dischargePdf($id)
{
    // 1️⃣ Get patient details
    $patient = DB::table('patients')->where('id', $id)->first();
    if (!$patient) {
        return redirect()->back()->with('error', 'Patient not found.');
    }

    // 2️⃣ Get billing base data
    $bill = DB::table('patient_bills')
        ->where('patient_id', $id)
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

    // 3️⃣ Admission & Discharge Dates
    $admissionDate = Carbon::parse($patient->date_of_admission);
    $dischargeDate = Carbon::now();

    // 4️⃣ Duration Calculations
    $daysStayed   = $admissionDate->diffInDays($dischargeDate);
    $monthsStayed = floor($daysStayed / 30);
    $extraDays    = $daysStayed % 30;

    // 5️⃣ Calculate Stay Charges
    $monthlyTotalAmount = $monthsStayed * $monthlyAmount;
    $extraDaysAmount    = $extraDays * $perDayAmount;
    $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
    $combinedPending = max($combinedTotalAmount - $receivedAmount, 0);

    // 6️⃣ Get Other Category-Wise Billing
    $medicalBillings = DB::table('patient_billings')
        ->where('patient_id', $id)
        ->select(
            'category',
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(received_amount) as total_received'),
            DB::raw('SUM(pending_amount) as total_pending')
        )
        ->groupBy('category')
        ->get();

    // 7️⃣ Add Room / Stay Charges Row
    $extraEntries = collect([
        (object)[
            'category' => 'Monthly Charges',
            'total_amount' => $combinedTotalAmount,
            'total_received' => $receivedAmount,
            'total_pending' => $combinedPending,
        ]
    ]);

    $allBillings = $extraEntries->merge($medicalBillings);

    // 8️⃣ Overall Totals
    $totalAmount   = $allBillings->sum('total_amount');
    $totalReceived = $allBillings->sum('total_received');
    $totalPending  = $allBillings->sum('total_pending');

    // 9️⃣ Generate PDF
    $pdf = Pdf::loadView('discharge_bill', compact(
        'patient',
        'admissionDate',
        'dischargeDate',
        'monthsStayed',
        'extraDays',
        'bill',
        'combinedTotalAmount',
        'combinedPending',
        'receivedAmount',
        'pickupAmount',
        'allBillings',
        'totalAmount',
        'totalReceived',
        'totalPending'
    ));

    $fileName = 'Discharge_Bill_' . str_replace(' ', '_', $patient->name) . '.pdf';
    return $pdf->download($fileName);
}

}

