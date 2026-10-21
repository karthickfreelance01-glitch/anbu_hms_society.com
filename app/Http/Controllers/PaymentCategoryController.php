<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('payment_categories')->get();
        return view('masters.payment_categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_category' => 'required|string|max:255',
        ]);

        DB::table('payment_categories')->insert([
            'payment_category' => $request->payment_category,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $category = DB::table('payment_categories')->where('id', $id)->first();
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_category' => 'required|string|max:255',
        ]);

        DB::table('payment_categories')->where('id', $id)->update([
            'payment_category' => $request->payment_category,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table('payment_categories')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Payment Category deleted successfully');
    }

 

public function accountTypeReports(Request $request)
{
    $hospitalAccounts = DB::table('payment_categories')->pluck('payment_category', 'id');

    $hospital_account_id = $request->hospital_account_id;
    $from_date = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
    $to_date = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

    // --- Medicine Bills ---
    $medicineBillsQuery = DB::table('medicine_bill')
        ->leftJoin('patients', 'medicine_bill.patient_id', '=', 'patients.id')
        ->select('medicine_bill.*', 'patients.name as patient_name');

    if ($hospital_account_id) {
        $medicineBillsQuery->where('medicine_bill.hospital_account', $hospital_account_id);
    }
    if ($from_date && $to_date) {
        $medicineBillsQuery->whereBetween('medicine_bill.issue_date', [$from_date, $to_date]);
    }
    $medicineBills = $medicineBillsQuery->get();
    $medicine_paid = $medicineBills->sum(fn($mbill) => floatval($mbill->initial_payment ?? 0));
    $medicine_pending = $medicineBills->sum(fn($mbill) => floatval($mbill->final_amount ?? 0));

    // --- Patient Bills ---
    $billingQuery = DB::table('patient_bills')
        ->leftJoin('patients', 'patient_bills.patient_id', '=', 'patients.id')
        ->select('patient_bills.*', 'patients.name as patient_name');

    if ($hospital_account_id) {
        $billingQuery->where('patient_bills.payment_category', $hospital_account_id);
    }
    if ($from_date && $to_date) {
        $billingQuery->whereBetween('patient_bills.bill_date', [$from_date, $to_date]);
    }
    $billing = $billingQuery->get();
    $billing_paid = 0;
    $billing_total = 0;
    foreach ($billing as $bill) {
        $totalAmount = floatval($bill->total_amount ?? 0);
        $initialPayment = floatval($bill->initial_payment ?? 0);

        $nextPayments = json_decode($bill->next_payments, true) ?? [];
        if ($from_date && $to_date) {
            $nextPayments = array_filter($nextPayments, function($p) use ($from_date, $to_date) {
                $date = Carbon::parse($p['date']);
                return $date->between($from_date, $to_date);
            });
        }
        $nextPaid = array_sum(array_map(fn($p) => floatval($p['amount'] ?? 0), $nextPayments));
        $billing_total += $totalAmount;
        $billing_paid += ($initialPayment + $nextPaid);
    }

    // --- Loans ---
    $loansQuery = DB::table('loans');
    if ($hospital_account_id) {
        $loansQuery->where('hospital_payment_category', $hospital_account_id);
    }
    if ($from_date && $to_date) {
        $loansQuery->whereBetween('loan_date', [$from_date, $to_date]);
    }
    $loans = $loansQuery->get();
    $loan_amount_total = $loans->sum(fn($loan) => floatval($loan->loan_amount ?? 0));

    // --- Expenses ---
    $expensesQuery = DB::table('expenses_details')
        ->join('expenses', 'expenses_details.expenses_id', '=', 'expenses.id')
        ->join('expensescategory', 'expenses_details.expense_category', '=', 'expensescategory.id')
        ->select(
            'expenses_details.*',
            'expenses.expense_name',
            'expensescategory.expensescategory as main_category'
        );
    if ($hospital_account_id) {
        $expensesQuery->where('expenses_details.hospital_payment_category', $hospital_account_id);
    }
    if ($from_date && $to_date) {
        $expensesQuery->whereBetween('expenses_details.expense_date', [$from_date, $to_date]);
    }
    $expenses = $expensesQuery->orderBy('expenses_details.expense_date', 'desc')->get();
    $overallExpenses = $expenses->sum('amount');

    // --- Totals ---
    $overallIncome = $medicine_paid + $billing_paid + $loan_amount_total;
    $overallPending = ($billing_total + $medicine_pending + $loan_amount_total) - ($billing_paid + $medicine_paid);
    $profit = $overallIncome - $overallExpenses;

    return view('payment_cat_reports', compact(
        'hospitalAccounts',
        'medicineBills',
        'billing',
        'loans',
        'expenses',
        'medicine_paid',
        'medicine_pending',
        'billing_paid',
        'billing_total',
        'loan_amount_total',
        'overallExpenses',
        'overallIncome',
        'overallPending',
        'profit',
        'hospital_account_id',
        'from_date',
        'to_date'
    ));
}


}
