<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientExpensesController extends Controller
{
    
   
    public function index(Request $request)
{
    $query = DB::table('patient_expenses')
        ->leftJoin('patients', 'patient_expenses.patient_id', '=', 'patients.id')
        ->select('patient_expenses.*', 'patients.name', 'patients.reg_no');

    // Filter by patient
    if ($request->patient_id) {
        $query->where('patient_expenses.patient_id', $request->patient_id);
    }

    // Get all expenses first
    $expenses = $query->orderBy('patient_expenses.date', 'desc')->get();

    // Map payments, calculate totals and status
    $expenses = $expenses->map(function($exp) {
        $payments = $exp->payments ? json_decode($exp->payments, true) : [];
        $received = array_sum(array_column($payments, 'amount'));
        $pending = $exp->total_amount - $received;

        $exp->amount_given = $exp->total_amount;
        $exp->amount_received = $received;
        $exp->pending_amount = $pending;
        $exp->status = $pending > 0 ? 'Given' : 'Received';
        $exp->payments_list = $payments;
        return $exp;
    });

    // Filter by status
    if ($request->status) {
        if ($request->status == 'Given') {
            if ($request->from_date && $request->to_date) {
                $expenses = $expenses->filter(function($e) use ($request) {
                    return $e->status == 'Given' && $e->date >= $request->from_date && $e->date <= $request->to_date;
                });
            } else {
                $expenses = $expenses->filter(fn($e) => $e->status == 'Given');
            }
        } elseif ($request->status == 'Received') {
            if ($request->from_date && $request->to_date) {
                $expenses = $expenses->map(function($e) use ($request) {
                    $filtered_payments = array_filter($e->payments_list, function($p) use ($request) {
                        return $p['date'] >= $request->from_date && $p['date'] <= $request->to_date;
                    });
                    $e->payments_list = $filtered_payments;
                    $e->amount_received = array_sum(array_column($filtered_payments, 'amount'));
                    $e->pending_amount = $e->total_amount - $e->amount_received;
                    $e->status = $e->pending_amount > 0 ? 'Given' : 'Received';
                    return $e;
                });
                // Keep only expenses with payments in the selected range
                $expenses = $expenses->filter(fn($e) => count($e->payments_list) > 0);
            } else {
                $expenses = $expenses->filter(fn($e) => $e->status == 'Received');
            }
        }
    }

    $patients = DB::table('patients')->get();

    return view('patient_other_expenses', compact('expenses', 'patients'));
}

    // Add Expense
    public function store(Request $request)
    {
        $amount = floatval($request->total_amount);

        DB::table('patient_expenses')->insert([
            'patient_id' => $request->patient_id,
            'total_amount' => $amount,
            'paid_amount' => 0,
            'payments' => json_encode([]),
            'notes' => $request->notes,
            'status' => 'Pending',
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Expense added successfully!']);
    }

    // Add Payment
    public function pay(Request $request, $id)
    {
        $expense = DB::table('patient_expenses')->where('id', $id)->first();
        if (!$expense) return response()->json(['message' => 'Expense not found'], 404);

        $paymentAmount = floatval($request->payment_amount);
        $payments = $expense->payments ? json_decode($expense->payments, true) : [];

        $payments[] = [
            'amount' => $paymentAmount,
            'date' => $request->payment_date ?? date('Y-m-d')
        ];

        $newPaidAmount = array_sum(array_column($payments, 'amount'));
        $status = ($expense->total_amount - $newPaidAmount) <= 0 ? 'Collected' : 'Pending';

        DB::table('patient_expenses')->where('id', $id)->update([
            'paid_amount' => $newPaidAmount,
            'payments' => json_encode($payments),
            'status' => $status,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Payment recorded successfully!']);
    }

    // Delete Expense
    public function destroy($id)
    {
        DB::table('patient_expenses')->where('id', $id)->delete();
        return response()->json(['message' => 'Expense deleted successfully!']);
    }
}
