<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingController extends Controller
{
    /** ================= CREATE BILL FORM ================= */
    public function create()
    {
        $patients = DB::table('patients')
            ->where('discharge_status', 'Pending')
            ->orderBy('name')
            ->get();

        $categories = [
            'pickup' => 'Patient Pick Charges',
            'medicines' => 'Medicines',
            'blood_test' => 'Blood Test',
            'outside_hospital' => 'Outside Hospital',
            'scan' => 'Scan',
            'other_expenses' => 'Other Expenses',
            'injections_fluids' => 'Injections / Fluids',
        ];

        $latestBill = DB::table('patient_billings')->latest('id')->first();
        $nextBillId = $latestBill ? $latestBill->id + 1 : 1;
        $billCode = 'B' . str_pad($nextBillId, 5, '0', STR_PAD_LEFT);

        return view('patient.billing.create', compact('patients', 'categories', 'billCode'));
    }

    /** ================= STORE BILL ================= */
    // public function store(Request $request)
    // {
    //     $billId = $request->bill_id;

    //     foreach ($request->category as $index => $category) {
    //         $amount = $request->amount[$index];
    //         $received = $request->received[$index] ?? 0;
    //         $pending = $amount - $received;

    //         DB::table('patient_billings')->insert([
    //             'patient_id' => $request->patient_id,
    //             'bill_id' => $billId,
    //             'bill_date' => $request->bill_date,
    //             'category' => $category,
    //             'amount' => $amount,
    //             'received_amount' => $received,
    //             'pending_amount' => $pending,
    //             'payment_mode' => $request->payment_mode,
    //             'account_type' => $request->accounttype,
    //             'notes' => $request->note[$index] ?? null,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     return response()->json(['success' => true, 'message' => 'Billing saved successfully!']);
    // }
    
    public function store(Request $request)
{
    $billId = $request->bill_id;

    foreach ($request->category as $index => $category) {

        $amount   = $request->amount[$index];
        $received = $request->received[$index] ?? 0;
        $pending  = $amount - $received;

        // ---- ALWAYS store a payment record ----
        $paymentDetails = [
            [
                'amount'       => $received,               // even if 0
                'date'         => $request->bill_date,
                'category'     => $category,
                'payment_mode' => $request->payment_mode,
            ]
        ];

        DB::table('patient_billings')->insert([
            'patient_id'       => $request->patient_id,
            'bill_id'          => $billId,
            'bill_date'        => $request->bill_date,
            'category'         => $category,
            'amount'           => $amount,
            'received_amount'  => $received,
            'pending_amount'   => $pending,
            'payment_mode'     => $request->payment_mode,
            'account_type'     => $request->accounttype,
            'notes'            => $request->note[$index] ?? null,

            // ⭐ Always store 1 entry
            'payment_details'  => json_encode($paymentDetails, JSON_UNESCAPED_UNICODE),

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Billing saved successfully!'
    ]);
}



    /** ================= BILL REPORT ================= */
    // public function patientbillreports()
    // {
    //     $patients = DB::table('patient_billings')
    //         ->leftJoin('patients', 'patient_billings.patient_id', '=', 'patients.id')
    //         ->select(
    //             'patient_billings.bill_id',
    //             'patients.id as patient_id',
    //             'patients.ip_number',
    //             'patients.name as patient_name',
    //             DB::raw('GROUP_CONCAT(DISTINCT patient_billings.category SEPARATOR ", ") as categories'),
    //             DB::raw('SUM(patient_billings.amount) as total_amount'),
    //             DB::raw('SUM(patient_billings.received_amount) as total_received'),
    //             DB::raw('(SUM(patient_billings.amount) - SUM(patient_billings.received_amount)) as total_pending'),
    //             DB::raw('MAX(patient_billings.payment_mode) as payment_mode'),
    //             DB::raw('MAX(patient_billings.account_type) as account_type'),
    //             DB::raw('MAX(patient_billings.bill_date) as bill_date')
    //         )
    //         ->groupBy('patient_billings.bill_id', 'patients.id', 'patients.ip_number', 'patients.name')
    //         ->where('patients.discharge_status', 'Pending')
    //         ->get();

    //     return view('patient.billing.report', compact('patients'));
    // }

    // /** ================= BILL DETAILS ================= */
    // public function getPatientBillingDetails($bill_id)
    // {
    //     $details = DB::table('patient_billings')
    //         ->leftJoin('patients', 'patient_billings.patient_id', '=', 'patients.id')
    //         ->where('patient_billings.bill_id', $bill_id)
    //         ->select(
    //             'patient_billings.id',
    //             'patients.name as patient_name',
    //             'patients.ip_number',
    //             'patient_billings.bill_id',
    //             'patient_billings.category',
    //             'patient_billings.amount',
    //             'patient_billings.received_amount',
    //             DB::raw('(patient_billings.amount - patient_billings.received_amount) as pending_amount'),
    //             'patient_billings.payment_mode',
    //             'patient_billings.created_at'
    //         )
    //         ->orderBy('patient_billings.id', 'asc')
    //         ->get();

    //     return response()->json($details);
    // }
    
     /** =========================================
     *  BILL REPORT (MAIN TABLE LIST)
     * ========================================= */
 public function patientbillreports()
{
    $patients = DB::table('patient_billings')
        ->leftJoin('patients', 'patient_billings.patient_id', '=', 'patients.id')
        ->select(
            'patient_billings.bill_id',
            'patients.id as patient_id',
            'patients.ip_number',
            'patients.name as patient_name',

            DB::raw('GROUP_CONCAT(DISTINCT patient_billings.id SEPARATOR ",") as billing_ids'),
            DB::raw('GROUP_CONCAT(DISTINCT patient_billings.category SEPARATOR ", ") as categories'),
            DB::raw('SUM(patient_billings.amount) as total_amount'),
            DB::raw('SUM(patient_billings.received_amount) as total_received'),
            DB::raw('(SUM(patient_billings.amount) - SUM(patient_billings.received_amount)) as total_pending'),

            DB::raw('MAX(patient_billings.payment_mode) as payment_mode'),
            DB::raw('MAX(patient_billings.account_type) as account_type'),
            DB::raw('MAX(patient_billings.payment_details) as payment_details')
        )
        ->where('patients.discharge_status', 'Pending')
        ->groupBy(
            'patient_billings.bill_id',
            'patients.id',
            'patients.ip_number',
            'patients.name'
        )
        ->get();

    foreach ($patients as $p) {
        $details = json_decode($p->payment_details, true);
        $p->bill_date = $details[0]['date'] ?? null;
    }

    return view('patient.billing.report', compact('patients'));
}



public function getPatientBillingDetails($bill_id)
{
    $items = DB::table('patient_billings')
        ->where('bill_id', $bill_id)
        ->select('id','category','amount','received_amount','pending_amount','payment_details')
        ->get();

    foreach ($items as $i) {
        $i->payment_history = json_decode($i->payment_details, true) ?? [];
    }

    return response()->json($items);
}




    /** ================= GET SINGLE BILL ITEM ================= */
    public function getBillingItem($id)
    {
        $item = DB::table('patient_billings')->where('id', $id)->first();
        return $item ? response()->json($item) : response()->json(['message' => 'Item not found'], 404);
    }

    /** ================= UPDATE BILL ITEM ================= */
//   public function updateBillingItem(Request $request, $id)
// {
//     // ✅ 1. Validate all input fields
//     $validated = $request->validate([
//         'category'          => 'nullable|string|max:255',
//         'description'       => 'nullable|string|max:500',
//         'quantity'          => 'nullable|numeric|min:0',
//         'rate'              => 'nullable|numeric|min:0',
//         'amount'            => 'required|numeric|min:0',
//         'received_amount'   => 'required|numeric|min:0',
//         'payment_mode'      => 'required|string|max:50',
//         'payment_reference' => 'nullable|string|max:255',
//         'remarks'           => 'nullable|string|max:500',
//         'bill_date'         => 'required|date', // ✅ Added validation
//     ]);

//     // ✅ 2. Check if the billing record exists
//     $item = DB::table('patient_billings')->where('id', $id)->first();
//     if (!$item) {
//         return response()->json(['message' => 'Billing record not found.'], 404);
//     }

//     // ✅ 3. Calculate derived values
//     $validated['pending_amount'] = max($validated['amount'] - $validated['received_amount'], 0);
//     $validated['updated_at'] = now();

//     // ✅ 4. Update record
//     DB::table('patient_billings')->where('id', $id)->update($validated);

//     // ✅ 5. Return success response
//     return response()->json([
//         'success' => true,
//         'message' => 'Billing item updated successfully.',
//     ]);
// }
public function updateBillingItem(Request $r, $id)
{
    $r->validate([
        "bill_date"        => "required|date",
        "category"         => "required",
        "amount"           => "required|numeric",
        "received_amount"  => "required|numeric",
        "payment_mode"     => "required",
        "json_index"       => "required|numeric"
    ]);

    $item = DB::table("patient_billings")->where("id", $id)->first();

    if (!$item) {
        return response()->json(["message" => "Not found"], 404);
    }

    // Decode JSON
    $details = json_decode($item->payment_details, true);
    if (!is_array($details)) $details = [];

    $i = $r->json_index;

    // Update only the selected JSON row
    if (isset($details[$i])) {
        $details[$i]['amount']       = $r->received_amount;
        $details[$i]['date']         = $r->bill_date;
        $details[$i]['category']     = $r->category;
        $details[$i]['payment_mode'] = $r->payment_mode;
    }

    // Recalculate received
    $total_received = array_sum(array_column($details, 'amount'));

    $pending = floatval($r->amount) - $total_received;

    DB::table("patient_billings")->where("id", $id)->update([
        "bill_date"        => $r->bill_date,
        "category"         => $r->category,
        "amount"           => $r->amount,
        "received_amount"  => $total_received,
        "pending_amount"   => $pending,
        "payment_mode"     => $r->payment_mode,
        "payment_details"  => json_encode($details),
        "updated_at"       => now(),
    ]);

    return response()->json(["message" => "Updated Successfully"]);
}






    /** ================= DELETE BILL ITEM ================= */
    public function deleteBillingItem($id)
    {
        $item = DB::table('patient_billings')->where('id', $id)->first();
        if (!$item) return response()->json(['message' => 'Item not found'], 404);

        DB::table('patient_billings')->where('id', $id)->delete();
        return response()->json(['message' => 'Billing item deleted successfully.']);
    }
    
    public function deleteBillingPayment(Request $r, $id)
{
    // Bill ID
    $billing = DB::table('patient_billings')->where('id', $id)->first();
    if (!$billing) {
        return response()->json(['message' => 'Billing record not found'], 404);
    }

    // Required key - JSON item ID
    $delete_item_id = $r->bill_item_id;  // <-- coming from AJAX

    // Decode JSON
    $details = json_decode($billing->payment_details, true);
    if (!is_array($details)) $details = [];

    // --------------------------------------
    // REMOVE ITEM FROM JSON
    // --------------------------------------
    $details = array_filter($details, function ($item) use ($delete_item_id) {
        return $item['bill_item_id'] != $delete_item_id;
    });

    // Re-index array (important)
    $details = array_values($details);

    // --------------------------------------
    // RECALCULATE TOTAL RECEIVED
    // --------------------------------------
    $total_received = 0;
    foreach ($details as $d) {
        $total_received += floatval($d['amount']);
    }

    // --------------------------------------
    // CALCULATE NEW PENDING
    // --------------------------------------
    $pending = floatval($billing->amount) - $total_received;

    // --------------------------------------
    // UPDATE DATABASE
    // --------------------------------------
    DB::table('patient_billings')->where('id', $id)->update([
        'received_amount'  => $total_received,
        'pending_amount'   => $pending,
        'payment_details'  => json_encode($details),
        'updated_at'       => now()
    ]);

    return response()->json([
        'message' => "Payment entry removed successfully.",
        'received_amount' => $total_received,
        'pending_amount'  => $pending,
        'payment_details' => $details
    ]);
}


    /** ================= PENDING CATEGORY ================= */
    public function getPendingCategories($patientId)
    {
        $pending = DB::table('patient_billings')
            ->select('category', DB::raw('SUM(pending_amount) as pending_amount'), DB::raw('MAX(bill_id) as bill_id'))
            ->where('patient_id', $patientId)
            ->where('pending_amount', '>', 0)
            ->groupBy('category')
            ->get();

        return response()->json($pending);
    }

    /** ================= ADD PAYMENT ================= */
    // public function addPayment(Request $request)
    // {
    //     $request->validate([
    //         'patient_id' => 'required',
    //         'category' => 'required',
    //         'received_amount' => 'required|numeric|min:0.01',
    //         'payment_mode' => 'required',
    //         'payment_date' => 'required|date',
    //     ]);

    //     $billing = DB::table('patient_billings')
    //         ->where('patient_id', $request->patient_id)
    //         ->where('category', $request->category)
    //         ->where('pending_amount', '>', 0)
    //         ->first();

    //     if (!$billing) {
    //         return response()->json(['error' => 'No pending amount found for this category'], 404);
    //     }

    //     $newReceived = $billing->received_amount + $request->received_amount;
    //     $newPending = max($billing->amount - $newReceived, 0);

    //     DB::table('patient_billings')
    //         ->where('id', $billing->id)
    //         ->update([
    //             'received_amount' => $newReceived,
    //             'pending_amount' => $newPending,
    //             'payment_mode' => $request->payment_mode,
    //             'updated_at' => now(),
    //         ]);

    //     return response()->json(['success' => true, 'message' => 'Payment added successfully.']);
    // }
    
   public function addPayment(Request $request)
{
    $request->validate([
        'billing_id'     => 'required',
        'category'       => 'required',
        'received_amount'=> 'required|numeric|min:1',
        'payment_date'   => 'required|date',
        'payment_mode'   => 'required'
    ]);

    // Get relevant billing row
    $billing = DB::table('patient_billings')
        ->where('bill_id', $request->billing_id)
        ->where('category', $request->category)
        ->first();

    if (!$billing) {
        return response()->json(['message' => 'Billing category not found'], 404);
    }

    // Update received & pending
    $newReceived = $billing->received_amount + $request->received_amount;
    $newPending  = max($billing->amount - $newReceived, 0);

    // Prepare payment details history JSON
    $paymentHistory = [];

    if (!empty($billing->payment_details)) {
        $paymentHistory = json_decode($billing->payment_details, true);
        if (!is_array($paymentHistory)) {
            $paymentHistory = [];
        }
    }

    // Add new record
    $paymentHistory[] = [
        'amount'       => $request->received_amount,
        'date'         => $request->payment_date,
        'category'     => $request->category,
        'payment_mode' => $request->payment_mode,
    ];

    // Update DB
    DB::table('patient_billings')
        ->where('id', $billing->id)
        ->update([
            'received_amount' => $newReceived,
            'pending_amount'  => $newPending,
            'payment_details' => json_encode($paymentHistory, JSON_UNESCAPED_UNICODE),
            'payment_mode'    => $request->payment_mode,
            'updated_at'      => now(),
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Payment added successfully.'
    ]);
}



    public function getPendingByBillingId($billId)
    {
        $billings = DB::table('patient_billings')
            ->where('bill_id', $billId)
            ->where('pending_amount', '>', 0)
            ->select('id', 'category', 'pending_amount', 'bill_id')
            ->get();
    
        return response()->json($billings);
    }


    /** ================= VIEW BILL ================= */
    public function viewBill($bill_id)
    {
        $bill = DB::table('patient_billings')
            ->leftJoin('patients', 'patient_billings.patient_id', '=', 'patients.id')
            ->select(
                'patients.id as patient_id',
                'patients.ip_number',
                'patients.name as patient_name',
                'patient_billings.bill_id',
                DB::raw('SUM(patient_billings.amount) as total_amount'),
                DB::raw('SUM(patient_billings.received_amount) as total_received'),
                DB::raw('(SUM(patient_billings.amount) - SUM(patient_billings.received_amount)) as total_pending'),
                DB::raw('MAX(patient_billings.created_at) as bill_date')
            )
            ->where('patient_billings.bill_id', $bill_id)
            ->groupBy('patients.id', 'patients.ip_number', 'patients.name', 'patient_billings.bill_id')
            ->first();

        if (!$bill) abort(404, 'Bill not found');

        $billingRecords = DB::table('patient_billings')
            ->where('bill_id', $bill_id)
            ->orderBy('id', 'asc')
            ->get();

        return view('patient.bill_view', compact('bill', 'billingRecords'));
    }
    
 
 
// public function reports(Request $request)
// {
//     $query = DB::table('patient_bills')
//         ->leftJoin('patients', 'patient_bills.patient_id', '=', 'patients.id')
//         ->leftJoin('patient_billings', 'patient_billings.patient_id', '=', 'patients.id')
//         ->select(
//             'patient_bills.id',
//             'patient_bills.patient_id',
//             'patient_bills.monthly_amount as default_monthly_amount',
//             'patient_bills.monthly_bills',
//             'patient_bills.received_amount',
//             'patient_bills.payments',
//             'patients.ip_number',
//             'patients.name',
//             'patients.contact_number',
//             'patients.date_of_admission',
//             'patients.discharge_date',
//             'patients.discharge_status',
//             DB::raw('COALESCE(SUM(patient_billings.amount),0) as billing_amount'),
//             DB::raw('COALESCE(SUM(patient_billings.received_amount),0) as billing_received')
//         )
//         ->where('patients.discharge_status', 'Pending')
//         ->groupBy(
//             'patient_bills.id',
//             'patient_bills.patient_id',
//             'patient_bills.monthly_amount',
//             'patient_bills.monthly_bills',
//             'patient_bills.received_amount',
//             'patient_bills.payments',
//             'patients.ip_number',
//             'patients.name',
//             'patients.contact_number',
//             'patients.date_of_admission',
//             'patients.discharge_date',
//             'patients.discharge_status'
//         );

//     // Apply filter
//     if ($request->filled('from_date') && $request->filled('to_date')) {
//         $query->whereBetween('patients.date_of_admission', [
//             $request->from_date,
//             $request->to_date
//         ]);
//     }

//     $reports = $query->orderBy('patients.date_of_admission', 'desc')->get()
//         ->map(function ($bill) {

//             $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true) ?: [];
//             $payments = json_decode($bill->payments ?? '[]', true) ?: [];

//             $admission = Carbon::parse($bill->date_of_admission);
//             $discharge = Carbon::parse($bill->discharge_date ?? now());

//             // Full months count
//             $monthsStayed = $admission->diffInMonths($discharge);

//             // Extra days
//             $extraDays = $admission->copy()->addMonths($monthsStayed)->diffInDays($discharge);

//             // Sort monthly_bills
//             usort($monthlyBills, fn($a, $b) => strtotime($a['from_date']) <=> strtotime($b['from_date']));

//             $filledBills = [];
//             $currentStart = $admission->copy();

//             // ================================
//             // FULL MONTH CALCULATION
//             // ================================
//             for ($i = 0; $i < $monthsStayed; $i++) {

//                 $monthStart = $currentStart->copy();
//                 $monthEnd   = $monthStart->copy()->addMonth();

//                 // Check active monthly rate
//                 $activeRate = (float)$bill->default_monthly_amount;
//                 foreach ($monthlyBills as $mb) {
//                     if ($monthStart->gte(Carbon::parse($mb['from_date']))) {
//                         $activeRate = (float)$mb['amount'];
//                     }
//                 }

//                 $filledBills[] = [
//                     'month_label' => ($i+1) . match($i+1) {
//                         1 => "st Month",
//                         2 => "nd Month",
//                         3 => "rd Month",
//                         default => "th Month"
//                     },
//                     'amount' => $activeRate,
//                     'period' => $monthStart->format('d-m-Y') . " to " . $monthEnd->format('d-m-Y'),
//                 ];

//                 $currentStart->addMonth();
//             }

//             // ================================
//             // EXTRA DAYS CALCULATION
//             // ================================
//             if ($extraDays > 0) {

//                 $lastRate = (float)$bill->default_monthly_amount;

//                 foreach ($monthlyBills as $mb) {
//                     if ($discharge->gte(Carbon::parse($mb['from_date']))) {
//                         $lastRate = (float)$mb['amount'];
//                     }
//                 }

//                 $dailyRate = $lastRate / 30;
//                 $partialAmount = round($dailyRate * $extraDays, 2);

//                 $extraStart = $discharge->copy()->subDays($extraDays);

//                 $filledBills[] = [
//                     'month_label' => "Extra Days",
//                     'amount' => $partialAmount,
//                     'period' => $extraStart->format('d-m-Y') . " to " . $discharge->format('d-m-Y'),
//                 ];
//             }

//             // ================================
//             // TOTAL CALCULATIONS
//             // ================================

//             // Monthly & extra days sum
//             $monthlyTotal = collect($filledBills)->sum('amount');

//             // Add patient_billings amounts
//             $billingAmount = (float)$bill->billing_amount;
//             $billingReceived = (float)$bill->billing_received;

//             $bill->filled_months = $filledBills;
//             $bill->months_stayed = $monthsStayed;
//             $bill->extra_days = $extraDays;

//             // FINAL TOTAL BILLED
//             $bill->total_billed = $monthlyTotal + $billingAmount;

//             // FINAL TOTAL PAID
//             $bill->total_paid = (float)$bill->received_amount + $billingReceived;

//             // PENDING
//             $bill->pending = $bill->total_billed - $bill->total_paid;

//             return $bill;
//         });

//     // Summary totals
//     $totalMonthly = $reports->sum('total_billed');
//     $totalReceived = $reports->sum('total_paid');
//     $totalPending = $reports->sum('pending');

//     return view('patient.income_reports', compact('reports', 'totalMonthly', 'totalReceived', 'totalPending'));
// }
public function reports(Request $request)
{
    $from = $request->from_date;
    $to   = $request->to_date;

    $query = DB::table('patient_bills')
        ->leftJoin('patients', 'patient_bills.patient_id', '=', 'patients.id')
        ->leftJoin('patient_billings', 'patient_billings.patient_id', '=', 'patients.id')
        ->select(
            'patient_bills.id',
            'patient_bills.patient_id',
            'patient_bills.monthly_amount as default_monthly_amount',
            'patient_bills.monthly_bills',
            'patient_bills.payments',
            'patients.ip_number',
            'patients.name',
            'patients.contact_number',
            'patients.date_of_admission',
            'patients.discharge_date',
            'patients.discharge_status',

            DB::raw('COALESCE(SUM(patient_billings.amount),0) AS billing_amount'),

            // IMPORTANT FIX
            DB::raw("GROUP_CONCAT(patient_billings.payment_details SEPARATOR '||') AS all_payment_details")
        )
        ->where('patients.discharge_status', 'Pending')
        ->groupBy(
            'patient_bills.id',
            'patient_bills.patient_id',
            'patient_bills.monthly_amount',
            'patient_bills.monthly_bills',
            'patient_bills.payments',
            'patients.ip_number',
            'patients.name',
            'patients.contact_number',
            'patients.date_of_admission',
            'patients.discharge_date',
            'patients.discharge_status'
        );

    $rows = $query->orderBy('patients.date_of_admission', 'desc')->get();

    $reports = $rows->map(function ($bill) use ($from, $to) {

        // -----------------------------
        // Decode monthly bill rates
        // -----------------------------
        $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true) ?: [];

        // -----------------------------
        // Decode patient_bills.payments JSON
        // -----------------------------
        $mainPayments = json_decode($bill->payments ?? '[]', true) ?: [];

        // -----------------------------
        // Decode patient_billings.payment_details (MULTIPLE ROWS)
        // -----------------------------
        $billingPayments = [];

        if (!empty($bill->all_payment_details)) {

            $rows = explode('||', $bill->all_payment_details);

            foreach ($rows as $jsonRow) {
                $decoded = json_decode($jsonRow, true);

                if (is_array($decoded)) {
                    foreach ($decoded as $entry) {
                        $billingPayments[] = $entry;
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // DATE FILTERING OF PAYMENTS
        // ---------------------------------------------------------------------
        $mainFiltered = collect($mainPayments)->filter(function ($p) use ($from, $to) {
            if (empty($p['amount']) || empty($p['date'])) return false;
            if (!$from || !$to) return true;
            return $p['date'] >= $from && $p['date'] <= $to;
        });

        $billingFiltered = collect($billingPayments)->filter(function ($p) use ($from, $to) {
            if (empty($p['amount']) || empty($p['date'])) return false;
            if (!$from || !$to) return true;
            return $p['date'] >= $from && $p['date'] <= $to;
        });

        $mainSum = $mainFiltered->sum(fn($p) => (float)$p['amount']);
        $billingSum = $billingFiltered->sum(fn($p) => (float)$p['amount']);

        // ---------------------------------------------------------------------
        // TOTAL PAID = ONLY DATE FILTERED VALUES (AS YOU REQUESTED)
        // ---------------------------------------------------------------------
        $bill->total_paid = $mainSum + $billingSum;

        // ---------------------------------------------------------------------
        // BILLING TOTAL (monthly + extra days + patient_billings.amount)
        // ---------------------------------------------------------------------
        $admission = Carbon::parse($bill->date_of_admission);
        $discharge = Carbon::parse($bill->discharge_date ?? now());

        $monthsStayed = $admission->diffInMonths($discharge);
        $extraDays = $admission->copy()->addMonths($monthsStayed)->diffInDays($discharge);

        $currentStart = $admission->copy();
        $filledBills = [];

        for ($i = 0; $i < $monthsStayed; $i++) {
            $rate = (float)$bill->default_monthly_amount;

            foreach ($monthlyBills as $m) {
                if ($currentStart->gte(Carbon::parse($m['from_date']))) {
                    $rate = (float)$m['amount'];
                }
            }

            $filledBills[] = [
                'amount' => $rate,
            ];

            $currentStart->addMonth();
        }

        if ($extraDays > 0) {
            $rate = (float)$bill->default_monthly_amount;

            foreach ($monthlyBills as $m) {
                if ($discharge->gte(Carbon::parse($m['from_date']))) {
                    $rate = (float)$m['amount'];
                }
            }

            $extraAmount = round(($rate / 30) * $extraDays, 2);

            $filledBills[] = [
                'amount' => $extraAmount
            ];
        }

        $monthlyTotal = collect($filledBills)->sum('amount');

        $bill->total_billed = $monthlyTotal + (float)$bill->billing_amount;

        $bill->pending = $bill->total_billed - $bill->total_paid;

        return $bill;
    });

    return view('patient.income_reports', [
        'reports' => $reports,
        'totalMonthly' => $reports->sum('total_billed'),
        'totalReceived' => $reports->sum('total_paid'),
        'totalPending' => $reports->sum('pending'),
    ]);
}
 
}
