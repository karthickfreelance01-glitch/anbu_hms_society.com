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

//   public function discharge($patientId)
// {
//     // 1️⃣ Get patient details
//     $patient = DB::table('patients')->where('id', $patientId)->first();
//     if (!$patient) {
//         return redirect()->back()->with('error', 'Patient not found.');
//     }

//     // 2️⃣ Get patient billing info
//     $bill = DB::table('patient_bills')
//         ->where('patient_id', $patientId)
//         ->select('monthly_amount', 'per_day', 'pick_up', 'payments')
//         ->first();

//     $monthlyAmount = $bill->monthly_amount ?? 0;
//     $perDayAmount  = $bill->per_day ?? 0;
//     $pickupAmount  = $bill->pick_up ?? 0;

//     // 3️⃣ Decode payments JSON and sum all "amount" values
//     $receivedAmount = 0;
//     if (!empty($bill->payments)) {
//         $paymentsData = json_decode($bill->payments, true);
//         if (is_array($paymentsData)) {
//             foreach ($paymentsData as $payment) {
//                 $receivedAmount += floatval($payment['amount'] ?? 0);
//             }
//         }
//     }

//     // 4️⃣ Admission & Discharge Dates
//     $admissionDate = \Carbon\Carbon::parse($patient->date_of_admission);
//     $dischargeDate = now();

//     // 5️⃣ Calculate duration (months + extra days)
//     $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
//     $afterFullMonths = $admissionDate->copy()->addMonths($monthsStayed);
//     $extraDays = $afterFullMonths->diffInDays($dischargeDate);

//     // 6️⃣ Calculate stay charges
//     $monthlyTotalAmount  = $monthsStayed * $monthlyAmount;
//     $extraDaysAmount     = $extraDays * $perDayAmount;
//     $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
//     $combinedPending     = max($combinedTotalAmount - $receivedAmount, 0);

//     // 7️⃣ Get other billings (category wise)
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

//     // 8️⃣ Add Monthly Charges row
//     $extraEntries = collect([
//         (object)[
//             'category'       => 'Monthly Charges',
//             'total_amount'   => $combinedTotalAmount,
//             'total_received' => $receivedAmount,
//             'total_pending'  => $combinedPending,
//         ]
//     ]);

//     $allBillings = $extraEntries->merge($medicalBillings);

//     // 9️⃣ Calculate grand totals
//     $totalAmount   = $allBillings->sum('total_amount');
//     $totalReceived = $allBillings->sum('total_received');
//     $totalPending  = $allBillings->sum('total_pending');

//     // 🔟 Return the discharge view
//     return view('patient.discharge', compact(
//         'patient',
//         'admissionDate',
//         'dischargeDate',
//         'monthsStayed',
//         'extraDays',
//         'monthlyAmount',
//         'perDayAmount',
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

// public function discharge($id)
// {
//     // 1️⃣ Get Patient Info
//     $patient = DB::table('patients')->where('id', $id)->first();
//     if (!$patient) {
//         return redirect()->back()->with('error', 'Patient not found.');
//     }

//     // 2️⃣ Billing Base Data
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

//     // 4️⃣ Count Full Months Stayed
//     $monthsStayed = 0;
//     $tempDate = $admissionDate->copy();

//     while (true) {
//         $nextMonth = $tempDate->copy()->addMonth();
//         if ($nextMonth <= $dischargeDate) {
//             $monthsStayed++;
//             $tempDate = $nextMonth;
//         } else {
//             break;
//         }
//     }

//     // 5️⃣ Calculate Extra Days (no 31-day logic)
//     $extraDays = $tempDate->diffInDays($dischargeDate);
//     $totalExtraDays = $extraDays;

//     // 6️⃣ Billing Calculations
//     $monthlyTotalAmount = $monthsStayed * $monthlyAmount;
//     $extraDaysAmount    = $totalExtraDays * $perDayAmount;
//     $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
//     $combinedPending = max($combinedTotalAmount - $receivedAmount, 0);

//     // 7️⃣ Fetch Category-wise Billing
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

//     // 8️⃣ Add Room / Stay Charges
//     $extraEntries = collect([
//         (object)[
//             'category' => 'Monthly Charges',
//             'total_amount' => $combinedTotalAmount,
//             'total_received' => $receivedAmount,
//             'total_pending' => $combinedPending,
//         ]
//     ]);

//     $allBillings = $extraEntries->merge($medicalBillings);

//     // 9️⃣ Totals
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
//         'totalExtraDays',
//         'monthlyAmount',
//         'perDayAmount',
//         'receivedAmount',
//         'pickupAmount',
//         'combinedTotalAmount',
//         'combinedPending',
//         'totalAmount',
//         'totalReceived',
//         'totalPending',
//         'allBillings'
//     ));
// }

 
public function discharge($id)
{
    $patient = DB::table('patients')->where('id', $id)->first();
    if (!$patient) {
        return redirect()->back()->with('error', 'Patient not found.');
    }

    $bill = DB::table('patient_bills')
        ->where('patient_id', $id)
        ->select('monthly_bills', 'payments', 'monthly_amount', 'total_amount')
        ->first();

    $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true) ?: [];
    $payments = json_decode($bill->payments ?? '[]', true) ?: [];

    $admissionDate = Carbon::parse($patient->date_of_admission);
    $dischargeDate = Carbon::parse(request('discharge_date') ?? Carbon::now()->format('Y-m-d'));

    $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
    $extraDays = $admissionDate->copy()->addMonths($monthsStayed)->diffInDays($dischargeDate);

    $filledBills = [];
    $currentStart = $admissionDate->copy();

    for ($i = 0; $i < $monthsStayed; $i++) {
        $monthStart = $currentStart->copy();
        $monthEnd = $monthStart->copy()->addMonth();

        $rate = $bill->monthly_amount ?? $bill->total_amount ?? 0;
        foreach ($monthlyBills as $mb) {
            if (Carbon::parse($mb['from_date'])->lessThanOrEqualTo($monthStart)) {
                $rate = $mb['amount'];
            }
        }

        $filledBills[] = [
            'category' => ($i+1) . " Month",
            'period' => $monthStart->format('d-m-Y') . " to " . $monthEnd->format('d-m-Y'),
            'total_amount' => (float)$rate
        ];

        $currentStart->addMonth();
    }

    if ($extraDays > 0) {
        $lastRate = $bill->monthly_amount ?? $bill->total_amount ?? 0;
        foreach ($monthlyBills as $mb) {
            if (Carbon::parse($mb['from_date'])->lessThanOrEqualTo($dischargeDate)) {
                $lastRate = $mb['amount'];
            }
        }

        $dailyRate = $lastRate / 30;
        $filledBills[] = [
            'category' => "Extra Days",
            'period' => $dischargeDate->copy()->subDays($extraDays)->format('d-m-Y') . " to " . $dischargeDate->format('d-m-Y'),
            'total_amount' => round($dailyRate * $extraDays, 2)
        ];
    }

    $roomTotal = collect($filledBills)->sum('total_amount');
    $roomReceived = collect($payments)->sum(fn($p) => $p['amount'] ?? 0);
    $roomPending = max(0, $roomTotal - $roomReceived);

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

    $monthlyEntry = collect([
        (object)[
            'category' => 'Monthly Charges',
            'total_amount' => $roomTotal,
            'total_received' => $roomReceived,
            'total_pending' => $roomPending,
        ]
    ]);

    $allBillings = $monthlyEntry->merge($otherBillings);

    $totalAmount = $allBillings->sum('total_amount');
    $totalReceived = $allBillings->sum('total_received');
    $totalPending = $allBillings->sum('total_pending');
    $monthlyRate = $bill->monthly_amount ?? 0;

    return view('patient.discharge', compact(
        'patient',
        'admissionDate',
        'dischargeDate',
        'monthsStayed',
        'extraDays',
        'filledBills',
        'allBillings',
        'totalAmount',
        'totalReceived',
        'totalPending',
        'monthlyRate'
    ));
}

public function recalculateBill(Request $request)
{
    $patientId = $request->patient_id;
    $dischargeDate = Carbon::parse($request->discharge_date);

    $patient = DB::table('patients')->find($patientId);
    $bill = DB::table('patient_bills')->where('patient_id', $patientId)->first();

    $monthlyBills = json_decode($bill->monthly_bills ?? '[]', true) ?: [];
    $admission = Carbon::parse($patient->date_of_admission);

    // Calculate Months & Extra Days
    $months = $admission->diffInMonths($dischargeDate);
    $extra = $admission->copy()->addMonths($months)->diffInDays($dischargeDate);

    $filled = [];
    $current = $admission->copy();
    $monthlyRate = (float)($monthlyBills[0]['amount'] ?? 0);

    // Build Monthly Rows
    for ($i = 0; $i < $months; $i++) {
        $start = $current->copy();
        $end   = $start->copy()->addMonth();

        $filled[] = [
            "category" => ($i + 1) . " Month",
            "period" => $start->format("d-m-Y") . " to " . $end->format("d-m-Y"),
            "total_amount" => $monthlyRate,
        ];

        $current->addMonth();
    }

    // Extra Days calculated as (extra days × monthly rate / 30)
    if ($extra > 0) {
        $start = $dischargeDate->copy()->subDays($extra);
        $extraAmount = ($extra * $monthlyRate) / 30;
    
        $filled[] = [
            "category" => "Extra Days",
            "period" => $start->format("d-m-Y") . " to " . $dischargeDate->format("d-m-Y"),
            "total_amount" => round($extraAmount, 2),
        ];
    }


    // Totals
    $totalRoomAmount = collect($filled)->sum("total_amount");

    $payments = json_decode($bill->payments ?? '[]', true);
    $received = collect($payments)->sum("amount");

    $pending = $totalRoomAmount - $received;

    // UPDATE CATEGORY-WISE SUMMARY
    $categorySummary = [
        [
            "category" => "Monthly Charges",
            "total" => $totalRoomAmount,
            "received" => $received,
            "pending" => $pending
        ]
    ];

    return response()->json([
        "filledBills" => $filled,
        "summary" => $categorySummary,
        "months" => $months,
        "extra" => $extra,
        "total" => $totalRoomAmount,
        "received" => $received,
        "pending" => $pending,
    ]);
}

public function storeDischargeDate(Request $request)
{
    $request->validate([
        'discharge_date' => 'required|date',
    ]);

    session(['discharge_date' => $request->discharge_date]);

    return response()->json(['status' => 'success', 'date' => $request->discharge_date]);
}

    public function confirmDischarge(Request $request)
    {
        // ✅ Validate all input
       

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

    // 2️⃣ Get bill info
    $bill = DB::table('patient_bills')
        ->where('patient_id', $patientId)
        ->select('monthly_amount', 'per_day', 'pick_up', 'discount', 'payments', 'final_amount', 'monthly_bills')
        ->first();

    if (!$bill) {
        $bill = (object)[
            'monthly_amount' => 0,
            'per_day' => 0,
            'pick_up' => 0,
            'discount' => 0,
            'payments' => null,
            'final_amount' => 0,
            'monthly_bills' => null
        ];
    }

    $monthlyAmount = floatval($bill->monthly_amount ?? 0);
    $perDayAmount  = floatval($bill->per_day ?? 0);
    $pickupAmount  = floatval($bill->pick_up ?? 0);
    $discount      = floatval($bill->discount ?? 0);
    $finalAmount   = floatval($bill->final_amount ?? 0);

    // 3️⃣ Payments total
    $receivedFromPayments = 0;
    if (!empty($bill->payments)) {
        $paymentsData = json_decode($bill->payments, true);
        if (is_array($paymentsData)) {
            foreach ($paymentsData as $payment) {
                $receivedFromPayments += floatval($payment['amount'] ?? 0);
            }
        }
    }
    $receivedAmount = $receivedFromPayments + $finalAmount;

    // 4️⃣ Dates
    $admissionDate = Carbon::parse($patient->date_of_admission)->startOfDay();
    $dischargeDate = Carbon::parse($patient->discharge_date)->startOfDay();

    // 5️⃣ Decode stored month bills
    $decodedBills = [];
    if (!empty($bill->monthly_bills)) {
        $decodedBills = json_decode($bill->monthly_bills, true) ?: [];
    }

    // 6️⃣ Function to build correct monthly breakdown
    $buildMonthlyBreakdown = function (Carbon $admissionDate, Carbon $dischargeDate, array $storedBills) {
        $bills = array_map(function ($r) {
            return [
                'from' => Carbon::parse($r['from_date']),
                'to'   => Carbon::parse($r['to_date']),
                'amount'=> (float) $r['amount'],
            ];
        }, $storedBills);

        $result = [];
        $pointer = $admissionDate->copy();
        $monthIndex = 0;

        $findAmountFor = function (Carbon $periodStart) use ($bills) {
            if (empty($bills)) return 0.0;

            foreach ($bills as $b) {
                if ($b['from']->isSameDay($periodStart)) return $b['amount'];
            }

            $periodEnd = $periodStart->copy()->addMonth();
            foreach ($bills as $bill) {
                if ($bill['from']->lt($periodEnd) && $bill['to']->gt($periodStart)) return $bill['amount'];
            }

            $best = null; $bestDiff = PHP_INT_MAX;
            foreach ($bills as $bill) {
                $diff = abs($bill['from']->diffInDays($periodStart));
                if ($diff < $bestDiff) { $bestDiff = $diff; $best = $bill; }
            }
            return $best ? $best['amount'] : 0.0;
        };

        while ($pointer->lt($dischargeDate)) {
            $periodStart = $pointer->copy();
            $periodEnd = $periodStart->copy()->addMonth();

            if ($periodEnd->lte($dischargeDate)) {
                $amount = $findAmountFor($periodStart);
                $label = ($monthIndex === 0) ? '1st Month' :
                         (($monthIndex === 1) ? '2nd Month' :
                         (($monthIndex === 2) ? '3rd Month' : ($monthIndex+1).'th Month'));

                $result[] = [
                    'label' => $label,
                    'amount' => round($amount, 2),
                    'from' => $periodStart->format('d-m-Y'),
                    'to' => $periodEnd->format('d-m-Y'),
                ];

                $pointer = $periodEnd;
                $monthIndex++;
                continue;
            } else {
                $extraDays = $dischargeDate->diffInDays($periodStart);
                if ($extraDays > 0) {
                    $matchedAmount = $findAmountFor($periodStart);
                    $perDay = $matchedAmount > 0 ? ($matchedAmount / 30) : 0;
                    $extraAmount = round($perDay * $extraDays, 2);


                    $result[] = [
                        'label' => 'Extra Days',
                        'amount' => $extraAmount,
                        'from' => $periodStart->format('d-m-Y'),
                        'to' => $dischargeDate->format('d-m-Y'),
                        'days' => $extraDays
                    ];
                }
                break;
            }
        }

        return $result;
    };

    // 7️⃣ Build final monthlyBills array
    $monthlyBills = $buildMonthlyBreakdown($admissionDate, $dischargeDate, $decodedBills);

    // 8️⃣ Totals
    $stayTotal = collect($monthlyBills)->sum('amount');

    // 9️⃣ Other billing categories
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

    $extraEntries = collect([
        (object)[
            'category' => 'stay_charges',
            'total_amount' => $stayTotal,
            'total_received' => $receivedAmount,
            'total_pending' => max($stayTotal - $receivedAmount, 0),
        ]
    ]);

    $allBillings = $extraEntries->merge($medicalBillings);

    $totalAmount   = $allBillings->sum('total_amount');
    $totalReceived = $allBillings->sum('total_received');
    $totalPending  = $allBillings->sum('total_pending');

    $monthsStayed = collect($monthlyBills)->filter(fn($m) => str_contains($m['label'], 'Month'))->count();
    $extraDaysEntry = collect($monthlyBills)->firstWhere('label', 'Extra Days');
    $extraDays = $extraDaysEntry['days'] ?? 0;

    return view('patient.discharge_printable', compact(
        'patient',
        'admissionDate',
        'dischargeDate',
        'bill',
        'monthlyBills',
        'monthsStayed',
        'extraDays',
        'monthlyAmount',
        'perDayAmount',
        'pickupAmount',
        'stayTotal',
        'receivedAmount',
        'allBillings',
        'totalAmount',
        'totalReceived',
        'totalPending',
        'discount'
    ));
}





public function dischargeedit($patientId)
{
    $patient = DB::table('patients')->where('id', $patientId)->first();
    if (!$patient) {
        return redirect()->back()->with('error', 'Patient not found.');
    }

    $bill = DB::table('patient_bills')
        ->where('patient_id', $patientId)
        ->select('monthly_amount', 'per_day', 'pick_up', 'payments', 'final_amount', 'discount', 'payment_mode')
        ->first();

    if (!$bill) {
        return redirect()->back()->with('error', 'Billing record not found.');
    }

    $monthlyAmount = floatval($bill->monthly_amount ?? 0);
    $perDayAmount  = floatval($bill->per_day ?? 0);
    $pickupAmount  = floatval($bill->pick_up ?? 0);
    $finalAmount   = floatval($bill->final_amount ?? 0);
    $discount      = floatval($bill->discount ?? 0);

    // Decode payments
    $receivedAmount = 0;
    if (!empty($bill->payments)) {
        $paymentsData = json_decode($bill->payments, true);
        if (is_array($paymentsData)) {
            foreach ($paymentsData as $payment) {
                $receivedAmount += floatval($payment['amount'] ?? 0);
            }
        }
    }

    // Admission & Discharge Dates
    $admissionDate = Carbon::parse($patient->date_of_admission);
    $dischargeDate = $patient->discharge_date ? Carbon::parse($patient->discharge_date) : now();

    // Stay Duration Calculation
    $monthsStayed = $admissionDate->diffInMonths($dischargeDate);
    $extraDays = 0;
    $tempDate = $admissionDate->copy();

    // Add +1 day for every 31-day month (exclude discharge month)
    for ($i = 0; $i < $monthsStayed; $i++) {
        if ($tempDate->daysInMonth == 31) {
            $extraDays++;
        }
        $tempDate->addMonth();
    }

    // Remaining days after full months
    $remainingDays = $admissionDate->copy()->addMonths($monthsStayed)->diffInDays($dischargeDate);

    // Stay Charges
    $monthlyTotalAmount  = $monthsStayed * $monthlyAmount;
    $extraDaysAmount     = ($remainingDays + $extraDays) * $perDayAmount;
    $combinedTotalAmount = $monthlyTotalAmount + $extraDaysAmount;
    $combinedPending     = max($combinedTotalAmount - $receivedAmount, 0);

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

    $extraEntries = collect([
        (object)[
            'category'       => 'Monthly Charges',
            'total_amount'   => $combinedTotalAmount,
            'total_received' => $receivedAmount,
            'total_pending'  => $combinedPending,
        ]
    ]);

    $allBillings = $extraEntries->merge($medicalBillings);

    $totalAmount   = $allBillings->sum('total_amount');
    $totalReceived = $allBillings->sum('total_received');
    $totalPending  = $allBillings->sum('total_pending');

    $discharge = $bill;

    return view('patient.discharge_edit', compact(
        'patient',
        'discharge',
        'admissionDate',
        'dischargeDate',
        'monthsStayed',
        'extraDays',
        'remainingDays',
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
        'finalAmount',
        'discount'
    ));
}



public function dischargeupdate(Request $request, $patientId)
{
    // ✅ Validate incoming data
  

    // ✅ Update patient_bills table
    DB::table('patient_bills')
        ->where('patient_id', $patientId)
        ->update([
            'final_amount' => $request->final_amount,
            'discount' => $request->discount ?? 0,
            'payment_mode' => $request->payment_mode,
            'updated_at' => now(),
        ]);

    // ✅ Update patients table (fixed spacing issue)
    DB::table('patients') 
        ->where('id', $patientId)
        ->update([
            'discharge_advice' => $request->discharge_advice,
            'discharge_date' => $request->discharge_date,
            'updated_at' => now(),
        ]);

    return redirect("patient/discharge_summary/{$patientId}")
    ->with('success', '✅ Patient discharge completed, advice saved, and payment recorded successfully!');
}


}


