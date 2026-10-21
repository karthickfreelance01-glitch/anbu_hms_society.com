<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineIssueController extends Controller
{
    // Show all medicine issues
public function index()
{
    $issues = DB::table('medicine_bill')
        ->join('patients', 'medicine_bill.patient_id', '=', 'patients.id')
        ->leftJoin('medicine_issues', 'medicine_bill.bill_id', '=', 'medicine_issues.bill_id')
        ->leftJoin('medicines', 'medicine_issues.drug_id', '=', 'medicines.id')
        ->select(
            'medicine_bill.bill_id',
            'patients.name as patient_name',
            DB::raw('GROUP_CONCAT(DISTINCT medicines.drug_name SEPARATOR ", ") as drugs'),
            DB::raw('GROUP_CONCAT(DISTINCT medicine_issues.drug_count SEPARATOR ", ") as drug_counts'),
            DB::raw('GROUP_CONCAT(DISTINCT medicine_issues.price SEPARATOR ", ") as prices'),
            DB::raw('MAX(medicine_bill.final_amount) as final_amount'),
            DB::raw('MAX(medicine_bill.pending_payment) as pending_payment'),
            DB::raw('MAX(medicine_bill.issue_date) as issue_date'),
            DB::raw('MAX(medicine_bill.issue_time) as issue_time')
        )
        ->groupBy('medicine_bill.bill_id', 'patients.name')
        
        ->get();

    return view('medicine_issues.index', compact('issues'));
}



   public function show($bill_id)
{
    // Fetch the main bill
    $bill = DB::table('medicine_bill')
        ->join('patients', 'medicine_bill.patient_id', '=', 'patients.id')
        ->select(
            'medicine_bill.*',
            'patients.name',
            'patients.reg_no'
        )
        ->where('medicine_bill.bill_id', $bill_id)
        ->first();

    if (!$bill) {
        return redirect()->back()->with('error', 'Bill not found');
    }

    // Fetch medicines for this bill
    $medicines = DB::table('medicine_issues')
        ->join('medicines', 'medicine_issues.drug_id', '=', 'medicines.id')
        ->select(
            'medicines.drug_name',
            'medicine_issues.drug_count',
            'medicine_issues.price'
        )
        ->where('medicine_issues.bill_id', $bill_id)
        ->get();

    // Calculate total
    $totalAmount = $medicines->sum(function($item) {
        return $item->price * $item->drug_count;
    });

    return view('medicine_issues.bill_view', compact('bill', 'medicines', 'totalAmount'));
}

    // Show form to create new issue
    public function create()
    {
        // Fetch patients
        $patients = DB::table('patients')->select('id', 'name', 'date_of_admission','reg_no')->get();

        // Fetch drugs with current stock > 0
        $drugs = DB::table('medicines')
            ->leftJoin('medicine_issues', 'medicines.id', '=', 'medicine_issues.drug_id')
            ->select(
                'medicines.id',
                'medicines.drug_name',
                'medicines.selling_price',
                'medicines.stock',
                DB::raw('IFNULL(SUM(medicine_issues.drug_count), 0) as issued_count')
            )
            ->groupBy('medicines.id', 'medicines.drug_name', 'medicines.stock',
            'medicines.selling_price')
            ->get()
            ->map(function($item) {
                $item->current_stock = $item->stock - $item->issued_count;
                return $item;
            });
            $hospitalAccounts = DB::table('payment_categories')->pluck('payment_category', 'id');


        return view('medicine_issues.create', compact('patients', 'drugs','hospitalAccounts'));
    }

    // Store medicine issue
//  public function store(Request $request)
// {
//     // Validate input
//     $request->validate([
//         'patient_id' => 'required|exists:patients,id',
//         'drugs.*.id' => 'required|exists:medicines,id',
//         'drugs.*.count' => 'required|integer|min:1',
//         'issue_time' => 'required',
//         'issue_date' => 'required|date',
//     ]);

//     // Insert each drug as a separate row
//     foreach ($request->drugs as $drug) {
//         DB::table('medicine_issues')->insert([
//             'patient_id' => $request->patient_id,
//             'drug_id'    => $drug['id'],
//             'drug_count' => $drug['count'],
//             'issue_time' => $request->issue_time,
//             'issue_date' => $request->issue_date,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);
//     }

//     return redirect('medicine-issues')->with('success', 'Medicine issued successfully!');
// }

    public function store(Request $request)
    {
        // ✅ Validate input
      

        // ✅ Generate a unique bill ID (example: BILL202510061230)
        $billId = 'BILL' . date('YmdHis');

        // ✅ Calculate totals
        $overallTotal = 0;
        foreach ($request->drugs as $drug) {
            $overallTotal += isset($drug['total']) ? $drug['total'] : ($drug['count'] * ($drug['price'] ?? 0));
        }
        $roundOff = round($overallTotal) - $overallTotal;
        $finalAmount = round($overallTotal);
        $initialPayment = $request->initial_payment ?? 0;
        $pendingPayment = $finalAmount - $initialPayment;

        // ✅ Insert each medicine issue with same bill_id
        foreach ($request->drugs as $drug) {
            DB::table('medicine_issues')->insert([
                'bill_id'    => $billId,
                'patient_id' => $request->patient_id,
                'drug_id'    => $drug['id'],
                'drug_count' => $drug['count'],
                'price'      => $drug['price'] ?? 0,
                'line_total' => $drug['total'] ?? 0,
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ✅ Store payment details separately
        DB::table('medicine_bill')->insert([
            'bill_id'         => $billId,
            'hospital_account' => $request->hospital_account_id,
            'patient_id'      => $request->patient_id,
            'overall_total'   => $overallTotal,
            'round_off'       => $roundOff,
            'final_amount'    => $finalAmount,
            'payment_mode'    => $request->payment_mode,
            'initial_payment' => $initialPayment,
            'pending_payment' => $pendingPayment,
            'issue_date'      => $request->issue_date,
            'issue_time'      => $request->issue_time,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ✅ Redirect with message
        return redirect('medicine-issues')->with('success', 'Bill generated and medicine issued successfully!');
    }

     public function discharge($id)
    {
        $issues = DB::table('medicine_bill')
            ->join('patients', 'medicine_bill.patient_id', '=', 'patients.id')
            ->leftJoin('medicine_issues', 'medicine_bill.bill_id', '=', 'medicine_issues.bill_id')
            ->leftJoin('medicines', 'medicine_issues.drug_id', '=', 'medicines.id')
            ->select(
                'medicine_bill.id',
                'medicine_bill.bill_id',
                'patients.name as patient_name',
                'medicine_bill.overall_total',
                'medicine_bill.payment_mode',
                'medicine_bill.issue_date',
                'medicine_bill.issue_time',
                DB::raw('GROUP_CONCAT(medicines.drug_name SEPARATOR ", ") as drugs'),
                DB::raw('GROUP_CONCAT(medicine_issues.drug_count SEPARATOR ", ") as drug_counts'),
                DB::raw('GROUP_CONCAT(medicine_issues.price SEPARATOR ", ") as prices')
            )
            ->groupBy(
                'medicine_bill.id',
                'medicine_bill.bill_id',
                'patients.name',
                'medicine_bill.overall_total',
                'medicine_bill.payment_mode',
                'medicine_bill.issue_date',
                'medicine_bill.issue_time'
            )
            ->orderByDesc('medicine_bill.id')
            ->get();

        return view('medicine_issues.index', compact('issues'));
    }

    // Show edit form
   public function edit($bill_id)
    {
        // Fetch the main bill
        $bill = DB::table('medicine_bill')
            ->where('bill_id', $bill_id)
            ->first();

        if (!$bill) {
            return redirect()->back()->with('error', 'Bill not found');
        }

        // Fetch patient info
        $patient = DB::table('patients')->where('id', $bill->patient_id)->first();

        // Fetch all patients for dropdown
        $patients = DB::table('patients')->select('id', 'name', 'date_of_admission')->get();

        // Fetch all medicines with current stock
        $drugs = DB::table('medicines')
            ->leftJoin('medicine_issues', 'medicines.id', '=', 'medicine_issues.drug_id')
            ->select(
                'medicines.id',
                'medicines.drug_name',
                'medicines.selling_price',
                'medicines.stock',
                DB::raw('IFNULL(SUM(medicine_issues.drug_count), 0) as issued_count')
            )
            ->groupBy('medicines.id', 'medicines.drug_name', 'medicines.stock', 'medicines.selling_price')
            ->get()
            ->map(function($item) {
                $item->current_stock = $item->stock - $item->issued_count;
                return $item;
            });

        // Fetch issued medicines for this bill
        $issuedDrugs = DB::table('medicine_issues')
            ->where('bill_id', $bill_id)
            ->get();

        return view('medicine_issues.edit', compact('bill', 'patient', 'patients', 'drugs', 'issuedDrugs'));
    }
    // Update medicine issue
    public function update(Request $request, $bill_id)
    {
        // ✅ Validate input
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'drugs' => 'required|array|min:1',
            'drugs.*.id' => 'required|exists:medicines,id',
            'drugs.*.count' => 'required|integer|min:1',
            'drugs.*.price' => 'nullable|numeric',
            'drugs.*.total' => 'nullable|numeric',
            'issue_time' => 'required',
            'issue_date' => 'required|date',
            'payment_mode' => 'nullable|string',
            'initial_payment' => 'nullable|numeric',
        ]);

        // ✅ Calculate totals
        $overallTotal = 0;
        foreach ($request->drugs as $drug) {
            $overallTotal += isset($drug['total']) ? $drug['total'] : ($drug['count'] * ($drug['price'] ?? 0));
        }
        $roundOff = round($overallTotal) - $overallTotal;
        $finalAmount = round($overallTotal);
        $initialPayment = $request->initial_payment ?? 0;
        $pendingPayment = $finalAmount - $initialPayment;

        // ✅ Update the main bill
        DB::table('medicine_bill')
            ->where('bill_id', $bill_id)
            ->update([
                'patient_id'      => $request->patient_id,
                'overall_total'   => $overallTotal,
                'round_off'       => $roundOff,
                'final_amount'    => $finalAmount,
                'payment_mode'    => $request->payment_mode,
                'initial_payment' => $initialPayment,
                'pending_payment' => $pendingPayment,
                'issue_date'      => $request->issue_date,
                'issue_time'      => $request->issue_time,
                'updated_at'      => now(),
            ]);

        // ✅ Delete old issued medicines for this bill
        DB::table('medicine_issues')->where('bill_id', $bill_id)->delete();

        // ✅ Insert updated issued medicines
        $insertData = [];
        foreach ($request->drugs as $drug) {
            if (!empty($drug['id'])) {
                $insertData[] = [
                    'bill_id'    => $bill_id,
                    'patient_id' => $request->patient_id,
                    'drug_id'    => $drug['id'],
                    'drug_count' => $drug['count'],
                    'price'      => $drug['price'] ?? 0,
                    'line_total' => $drug['total'] ?? ($drug['count'] * ($drug['price'] ?? 0)),
                    'issue_date' => $request->issue_date,
                    'issue_time' => $request->issue_time,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($insertData)) {
            DB::table('medicine_issues')->insert($insertData);
        }

        // ✅ Redirect with success message
        return redirect('medicine-issues')->with('success', 'Medicine issue updated successfully!');
    }



    // Delete medicine issue
    public function destroy($bill_id)
{
    // ✅ Delete issued medicines first
    DB::table('medicine_issues')->where('bill_id', $bill_id)->delete();

    // ✅ Delete the main bill
    DB::table('medicine_bill')->where('bill_id', $bill_id)->delete();

    // ✅ Redirect with success message
    return redirect('medicine-issues')->with('success', 'Bill and associated medicines deleted successfully!');
}


    // Fetch patient details by ID (for AJAX)
    public function getPatient($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        return response()->json($patient);
    }

    public function stock()
    {
        $medicines = DB::table('medicines')
            ->leftJoin('medicine_issues', 'medicines.id', '=', 'medicine_issues.drug_id')
            ->select(
                'medicines.id',
                'medicines.drug_name',
                'medicines.category_id',
                'medicines.manufactured_date',
                'medicines.expiry_date',
                'medicines.price',
                'medicines.selling_price',
                'medicines.stock',
                DB::raw('IFNULL(SUM(medicine_issues.drug_count), 0) as issued_count')
            )
            ->groupBy(
                'medicines.id',
                'medicines.drug_name',
                'medicines.category_id',
                'medicines.manufactured_date',
                'medicines.expiry_date',
                'medicines.price',
                'medicines.selling_price',
                'medicines.stock'
            )
            ->get();

        return view('medicine_issues.stock', compact('medicines'));
    }

    // Show report filter form
   public function report()
    {
        $patients = DB::table('patients')->select('id', 'name','reg_no')->get();
        $issues = collect(); // Empty collection initially
        return view('medicine_issues.report', compact('patients', 'issues'));
    }

    // Filter report by patient
    public function reportFilter(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer',
        ]);

        $patients = DB::table('patients')->select('id', 'name')->get();

        $issues = DB::table('medicine_issues')
            ->join('patients', 'medicine_issues.patient_id', '=', 'patients.id')
            ->leftJoin('medicines', 'medicine_issues.drug_id', '=', 'medicines.id')
            ->leftJoin('medicine_bill', 'medicine_issues.bill_id', '=', 'medicine_bill.bill_id')
            ->select(
                'medicine_issues.*',
                'patients.name as patient_name',
                'patients.reg_no',
                'patients.date_of_admission',
                'medicines.drug_name',
                'medicine_bill.issue_date',
                'medicine_bill.issue_time'
            )
            ->where('medicine_issues.patient_id', $request->patient_id)
            ->get();

        return view('medicine_issues.report', compact('patients', 'issues'))
            ->with('selected_patient', $request->patient_id);
    }


    public function addNextPayment(Request $request)
{
   

    // Fetch bill details
    $bill = DB::table('medicine_bill')->where('bill_id', $request->bill_id)->first();

    if (!$bill) {
        return response()->json(['message' => 'Bill not found!'], 404);
    }

    if ($bill->pending_payment <= 0) {
        return response()->json(['message' => 'No pending amount to pay!'], 400);
    }

    // Insert next payment into medicine_payments table
    DB::table('medicine_bill')->insert([
        'bill_id' => $request->bill_id,
        'patient_id' => $bill->patient_id, // store patient_id
        'initial_payment' => $request->next_payment_amount,
        'issue_date' => $request->next_payment_date,
        'payment_mode' => $request->payment_mode,
        'hospital_account' => $request->hospital_account,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Update pending payment in medicine_bill table
    $newPending = $bill->pending_payment - $request->next_payment_amount;
    DB::table('medicine_bill')->where('bill_id', $request->bill_id)
        ->update(['pending_payment' => $newPending]);

    return response()->json(['message' => 'Next payment added successfully!']);
}


public function getPayments($bill_id)
{
    // Get all bill entries for this bill_id
    $bills = DB::table('medicine_bill')
        ->where('bill_id', $bill_id)
        ->orderBy('id', 'asc') // ensures payments are in chronological order
        ->get();

    if ($bills->isEmpty()) {
        return response()->json(['message' => 'Bill not found'], 404);
    }

    // Build payments array
    $payments = [];
    foreach ($bills as $bill) {
        $payments[] = [
            'payment_date' => \Carbon\Carbon::parse($bill->issue_date)->format('d-m-Y'),
            'amount' => $bill->initial_payment ?? 0, // or whichever column stores the payment
            'payment_mode' => ucfirst($bill->payment_mode),
            'hospital_account' => DB::table('payment_categories')->where('id', $bill->hospital_account)->value('payment_category') ?? 'N/A',
        ];
    }

    // Use the first row for main bill info
    $mainBill = $bills->first();

    return response()->json([
        'issue_date' => \Carbon\Carbon::parse($mainBill->issue_date)->format('d-m-Y'),
        'payment_mode' => ucfirst($mainBill->payment_mode),
        'hospital_account' => DB::table('payment_categories')->where('id', $mainBill->hospital_account)->value('payment_category') ?? 'N/A',
        'total_amount' => $mainBill->overall_total,
        'pending_amount' => $mainBill->pending_payment,
        'payments' => $payments
    ]);
}



}
