<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function create()
    {
        $patients = DB::table('patients')->orderBy('name')->get();

        $categories = [
            
            'pickup' => 'Patient Pick Charges',
            'medicines' => 'Medicines',
            'blood_test' => 'Blood Test',
            'scan' => 'Scan',
            'other_expenses' => 'Other Expenses',
            'injections_fluids' => 'Injections / Fluids',
        ];

        return view('patient.billing.create', compact('patients', 'categories'));
    }

   public function store(Request $request)
{
   

    $patientId = $request->patient_id;
   
    $billDate = $request->bill_date;
    $categories = $request->category;
    $amounts = $request->amount;
    $receivedAmounts = $request->received ?? [];
    $notes = $request->note ?? []; // ✅ Added note array
    $paymentMode = $request->payment_mode;
    $accounttype = $request->accounttype;
     //dd( $patientId);
    foreach ($categories as $index => $category) {
        $amount = $amounts[$index];
        $received = $receivedAmounts[$index] ?? 0;
        $pending = $amount - $received;
        $note = $notes[$index] ?? null; // ✅ Capture individual note


        DB::table('patient_billings')->insert([
            'patient_id' => $patientId,
            'category' => $category,
            'bill_date' => $billDate,   
            'amount' => $amount,
            'received_amount' => $received,
            'pending_amount' => $pending,
            'payment_mode' => $paymentMode,
            'account_type' => $accounttype,
            'notes' => $note, // ✅ Store note
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return redirect()->route('patient.billing.create')->with('success', 'Billing added successfully!');
}


public function patientbillreports()
{
    // Get unique patient list (summary)
    $patients = DB::table('patient_billings')
        ->leftJoin('patients', 'patient_billings.patient_id', '=', 'patients.id')
        ->select(
            'patients.id as patient_id',
            'patients.ip_number',
            'patients.name as patient_name',
            DB::raw('SUM(patient_billings.amount) as total_amount'),
            DB::raw('SUM(patient_billings.received_amount) as total_received'),
            DB::raw('(SUM(patient_billings.amount) - SUM(patient_billings.received_amount)) as total_pending'),
            DB::raw('MAX(patient_billings.payment_mode) as payment_mode'),
            DB::raw('MAX(patient_billings.created_at) as created_at')
        )
        ->groupBy('patients.id', 'patients.ip_number', 'patients.name')
        ->orderBy('patients.id', 'desc')
        ->get();

    return view('patient.billing.report', compact('patients'));
}

// ✅ Ajax function for modal details
public function getPatientBillingDetails($patient_id)
{
    $details = DB::table('patient_billings')
        ->where('patient_id', $patient_id)
        ->select('category', 'amount', 'received_amount', 'payment_mode', 'created_at')
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($details);
}

public function reports(Request $request)
{
    $query = DB::table('patient_bills')
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
            'patient_bills.payments',
            DB::raw('(patient_bills.monthly_amount - patient_bills.received_amount) as pending_amount')
        )
        ->where('patients.discharge_status', 'Pending');

    // Apply date filters
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('patients.date_of_admission', [
            $request->from_date,
            $request->to_date
        ]);
    }

    $reports = $query->orderBy('patients.date_of_admission', 'desc')->get();

    // Calculate totals
    $totalMonthly = $reports->sum('monthly_amount');
    $totalReceived = $reports->sum('received_amount');
    $totalPending = $reports->sum('pending_amount');

    return view('patient.income_reports', compact('reports', 'totalMonthly', 'totalReceived', 'totalPending'));
}

}
