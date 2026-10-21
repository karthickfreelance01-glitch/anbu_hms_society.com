<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DischargeController extends Controller
{
//       public function discharge($patientId)
// {
//     // 1️⃣ Get patient details
//     $patient = DB::table('patients')->where('id', $patientId)->first();
//     if (!$patient) {
//         return redirect()->back()->with('error', 'Patient not found.');
//     }

//     // 2️⃣ Get billing base data
//     $bill = DB::table('patient_bills')
//         ->where('patient_id', $patientId)
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

//     // 4️⃣ Calendar-based Duration
//     $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
//     $afterFullMonths = $admissionDate->copy()->addMonths($monthsStayed);
//     $extraDays = $afterFullMonths->diffInDays($dischargeDate);

//     // 5️⃣ Calculate Stay Charges
//     $monthlyTotalAmount = $monthsStayed * $monthlyAmount;
//     $extraDaysAmount    = $extraDays * $perDayAmount;
//     $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
//     $combinedPending = max($combinedTotalAmount - $receivedAmount, 0);

//     // 6️⃣ Get Other Category-Wise Billing
//     $medicalBillings = DB::table('patient_billings')
//         ->where('patient_id', $patientId)
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
//             'category' => 'Monthly Charges',
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

//     // 🔟 Return View
//     return view('patient.discharge', compact(
//         'patient',
//         'admissionDate',
//         'dischargeDate',
//         'monthsStayed',
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
// }

    public function discharge($patientId)
{
    // 1️⃣ Get patient details
    $patient = DB::table('patients')->where('id', $patientId)->first();
    if (!$patient) {
        return redirect()->back()->with('error', 'Patient not found.');
    }

    // 2️⃣ Get billing data
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

    // 3️⃣ Admission & Discharge Dates
    $admissionDate = Carbon::parse($patient->date_of_admission);
    $dischargeDate = Carbon::now();

    // 4️⃣ Calendar-based Duration
    $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
    $afterFullMonths = $admissionDate->copy()->addMonths($monthsStayed);
    $extraDays = $afterFullMonths->diffInDays($dischargeDate);

    // 5️⃣ Calculate Stay Charges
    $monthlyTotalAmount = $monthsStayed * $monthlyAmount;
    $extraDaysAmount    = $extraDays * $perDayAmount;
    $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
    $combinedPending = max($combinedTotalAmount - $receivedAmount, 0);

    // 6️⃣ Get Other Billings
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

    // 7️⃣ Add Monthly Charges Entry
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

    // 🔟 Return View
    return view('patient.discharge', compact(
        'patient',
        'admissionDate',
        'dischargeDate',
        'monthsStayed',
        'extraDays',
        'bill',
        'monthlyAmount',
        'perDayAmount',
        'combinedTotalAmount',
        'combinedPending',
        'receivedAmount',
        'pickupAmount',
        'allBillings',
        'totalAmount',
        'totalReceived',
        'totalPending'
    ));
}


    public function confirmDischarge(Request $request)
    {
        // ✅ Validate all input
        $request->validate([
            'patient_id'      => 'required|integer|exists:patients,id',
            'discharge_date'  => 'required|date',
            'payment_mode'    => 'required|string|max:50',
            'discount'        => 'nullable|numeric|min:0',
            'final_amount'    => 'required|numeric|min:0',
            'discharge_advice'=> 'nullable|string|max:1000',
        ]);

        $patientId       = $request->patient_id;
        $dischargeDate   = $request->discharge_date;
        $paymentMode     = $request->payment_mode;
        $discount        = $request->discount ?? 0;
        $finalAmount     = $request->final_amount;
        //dd( $finalAmount );
        $dischargeAdvice = $request->discharge_advice;
        //dd($dischargeDate);
        // ✅ Step 1: Update patient discharge info
        DB::table('patients')
            ->where('id', $patientId)
            ->update([
                'discharge_status' => 'Discharged',
                'discharge_date'   => $dischargeDate,
                'discharge_advice' => $dischargeAdvice,
                'updated_at'       => now(),
            ]);

        // ✅ Step 2: Fetch patient billing info
        $bill = DB::table('patient_bills')->where('patient_id', $patientId)->first();

        if ($bill) {
            // ✅ Step 3: Update existing billing record
            DB::table('patient_bills')
                ->where('patient_id', $patientId)
                ->update([
                    'final_amount'    => $finalAmount,
                    'discount'        => $discount,
                    'payment_mode'    => $paymentMode,
                    'received_amount' => DB::raw('received_amount + ' . $finalAmount),
                    'pending_amount'  => DB::raw('GREATEST(pending_amount - ' . $finalAmount . ', 0)'),
                    'updated_at'      => now(),
                ]);
        } else {
            // ✅ Step 4: Create new billing record
            DB::table('patient_bills')->insert([
                'patient_id'      => $patientId,
                'final_amount'    => $finalAmount,
                'discount'        => $discount,
                'payment_mode'    => $paymentMode,
                'received_amount' => $finalAmount,
                'pending_amount'  => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        return redirect("patient/discharge_summary/{$patientId}")
    ->with('success', '✅ Patient discharge completed, advice saved, and payment recorded successfully!');
    }

//   public function showDischargeSummary($patientId)
//     {
//         // 1️⃣ Fetch Patient Details
//         $patient = DB::table('patients')->where('id', $patientId)->first();
//         if (!$patient) {
//             return redirect()->back()->with('error', 'Patient not found.');
//         }

//         // 2️⃣ Fetch Billing Information
//         $bill = DB::table('patient_bills')
//             ->where('patient_id', $patientId)
//             ->select(
//                 DB::raw('SUM(monthly_amount) as monthly_amount'),
//                 DB::raw('SUM(per_day) as per_day'),
//                 DB::raw('SUM(received_amount) as received_amount'),
//                 DB::raw('SUM(pick_up) as pickup_amount'),
//                 DB::raw('SUM(discount) as discount')
//             )
//             ->first();

//         // Safe conversions
//         $monthlyAmount  = floatval($bill->monthly_amount ?? 0);
//         $perDayAmount   = floatval($bill->per_day ?? 0);
//         $receivedAmount = floatval($bill->received_amount ?? 0);
//         $pickupAmount   = floatval($bill->pickup_amount ?? 0);
//         $discount       = floatval($bill->discount ?? 0);

//         // 3️⃣ Admission & Discharge Dates
//         $admissionDate = Carbon::parse($patient->date_of_admission);
//         $dischargeDate = Carbon::now(); // Or $patient->date_of_discharge if exists

//         $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
//         $daysStayed   = $admissionDate->diffInDays($dischargeDate);
//         $extraDays    = max(0, $daysStayed - ($monthsStayed * 30));

//         // 4️⃣ Calculate amounts
//         $monthlyTotalAmount = $monthlyAmount * $monthsStayed;
//         $extraDaysAmount    = $perDayAmount * $extraDays;
//         $monthlyTotalPending = $monthlyTotalAmount + $extraDaysAmount - $receivedAmount;

//         // 5️⃣ Category-wise billing from patient_billings
//         $medicalBillings = DB::table('patient_billings')
//             ->where('patient_id', $patientId)
//             ->select(
//                 'category',
//                 DB::raw('COALESCE(SUM(amount),0) as total_amount'),
//                 DB::raw('COALESCE(SUM(received_amount),0) as total_received'),
//                 DB::raw('COALESCE(SUM(pending_amount),0) as total_pending')
//             )
//             ->groupBy('category')
//             ->get();

//         // 6️⃣ Add extra entries for monthly, extra days, pickup
//         $extraEntries = collect([
//             (object)[
//                 'category' => 'monthly',
//                 'total_amount' => $monthlyTotalAmount,
//                 'total_received' => $receivedAmount,
//                 'total_pending' => $monthlyTotalPending,
//             ],
//             (object)[
//                 'category' => 'extra_days',
//                 'total_amount' => $extraDaysAmount,
//                 'total_received' => 0,
//                 'total_pending' => $extraDaysAmount,
//             ],
//             (object)[
//                 'category' => 'pickup',
//                 'total_amount' => $pickupAmount,
//                 'total_received' => 0,
//                 'total_pending' => $pickupAmount,
//             ],
//         ]);

//         $allBillings = $medicalBillings->merge($extraEntries);

//         // 7️⃣ Totals
//         $totalAmount   = $allBillings->sum('total_amount');
//         $totalReceived = $allBillings->sum('total_received');
//         $totalPending  = $allBillings->sum('total_pending');

//         // 8️⃣ Return view
//         return view('patient.discharge_printable', compact(
//             'patient',
//             'admissionDate',
//             'dischargeDate',
//             'monthsStayed',
//             'extraDays',
//             'monthlyAmount',
//             'perDayAmount',
//             'discount',
//             'receivedAmount',
//             'pickupAmount',
//             'monthlyTotalAmount',
//             'extraDaysAmount',
//             'monthlyTotalPending',
//             'allBillings',
//             'totalAmount',
//             'totalReceived',
//             'totalPending'
//         ));
//     }


public function showDischargeSummary($patientId)
{
    // 1️⃣ Get patient details
    $patient = DB::table('patients')->where('id', $patientId)->first();
    if (!$patient) {
        return redirect()->back()->with('error', 'Patient not found.');
    }

    // 2️⃣ Get billing base data safely
    $bill = DB::table('patient_bills')
        ->where('patient_id', $patientId)
        ->select(
            DB::raw('SUM(monthly_amount) as monthly_amount'),
            DB::raw('SUM(per_day) as per_day'),
            DB::raw('SUM(received_amount) as received_amount'),
            DB::raw('SUM(pick_up) as pickup_amount'),
            DB::raw('SUM(discount) as discount')
        )
        ->first();

    // Handle nulls safely
    $monthlyAmount   = floatval($bill->monthly_amount ?? 0);
    $perDayAmount    = floatval($bill->per_day ?? 0);
    $receivedAmount  = floatval($bill->received_amount ?? 0);
    $pickupAmount    = floatval($bill->pickup_amount ?? 0);
    $discount        = floatval($bill->discount ?? 0);

    // 3️⃣ Admission & Discharge Dates
     $admissionDate = Carbon::parse($patient->date_of_admission);
    $dischargeDate = Carbon::parse($patient->discharge_date);

    // 4️⃣ Calendar-based Duration
    $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
    $afterFullMonths = $admissionDate->copy()->addMonths($monthsStayed);
    $extraDays = $afterFullMonths->diffInDays($dischargeDate);

    // 5️⃣ Calculate Stay Charges
    $monthlyTotalAmount  = $monthsStayed * $monthlyAmount;
    $extraDaysAmount     = $extraDays * $perDayAmount;
    $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
    $combinedPending     = max($combinedTotalAmount - $receivedAmount, 0);

    // 6️⃣ Get Category-Wise Billings
    $billings = DB::table('patient_billings')
        ->where('patient_id', $patientId)
        ->select(
            'category',
            DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
            DB::raw('COALESCE(SUM(received_amount), 0) as total_received'),
            DB::raw('COALESCE(SUM(pending_amount), 0) as total_pending')
        )
        ->groupBy('category')
        ->get();

    // 7️⃣ Add Monthly Stay Charges as Extra Entry
    $extraEntries = collect([
        (object)[
            'category'        => 'Monthly Charges',
            'total_amount'    => $combinedTotalAmount,
            'total_received'  => $receivedAmount,
            'total_pending'   => $combinedPending,
        ],
    ]);

    // Merge stay + other category billings
    $allBillings = $extraEntries->merge($billings);

    // 8️⃣ Overall Totals
    $totalAmount   = $allBillings->sum('total_amount');
    $totalReceived = $allBillings->sum('total_received');
    $totalPending  = $allBillings->sum('total_pending');

    // 🔟 Return to View
    return view('patient.discharge_printable', compact(
        'patient',
        'admissionDate',
        'dischargeDate',
        'monthsStayed',
        'extraDays',
        'bill',
        'billings',
        'monthlyAmount',
        'perDayAmount',
        'monthlyTotalAmount',
        'extraDaysAmount',
        'combinedTotalAmount',
        'combinedPending',
        'receivedAmount',
        'pickupAmount',
        'allBillings',
        'totalAmount',
        'totalReceived',
        'totalPending',
        'discount'
    ));
}


     

}
