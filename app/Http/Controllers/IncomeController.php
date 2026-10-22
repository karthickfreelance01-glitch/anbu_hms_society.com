<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function create()
    {
        $patients = $this->patientOptions();

        return view('income.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $isHandCash = $request->input('patient_id') === 'hand_cash';
        $validated = $request->validate([
            'patient_id' => $isHandCash ? 'required|in:hand_cash' : 'required|exists:patients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash,Hand Cash',
        ]);

        DB::table('income_entries')->insert([
            'patient_id' => $isHandCash ? null : $validated['patient_id'],
            'income_date' => $validated['date'],
            'amount' => $validated['amount'],
            'payment_mode' => $isHandCash ? 'Hand Cash' : $validated['payment_mode'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('income.ledger.reports')->with('success', 'Income recorded successfully.');
    }

    public function edit($id)
    {
        $income = DB::table('income_entries')->where('id', $id)->first();
        abort_unless($income, 404);

        $patients = $this->patientOptions();

        return view('income.edit', compact('income', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $isHandCash = $request->input('patient_id') === 'hand_cash';
        $validated = $request->validate([
            'patient_id' => $isHandCash ? 'required|in:hand_cash' : 'required|exists:patients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash,Hand Cash',
        ]);

        $query = DB::table('income_entries')->where('id', $id);
        abort_unless((clone $query)->exists(), 404);

        $query->update([
            'patient_id' => $isHandCash ? null : $validated['patient_id'],
            'income_date' => $validated['date'],
            'amount' => $validated['amount'],
            'payment_mode' => $isHandCash ? 'Hand Cash' : $validated['payment_mode'],
            'updated_at' => now(),
        ]);

        return redirect()->route('income.ledger.reports')->with('success', 'Income updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = DB::table('income_entries')->where('id', $id)->delete();
        abort_unless($deleted, 404);

        return redirect()->route('income.ledger.reports')->with('success', 'Income deleted successfully.');
    }

    public function reports(Request $request)
    {
        $patients = DB::table('patients')
            ->select('id')
            ->selectRaw('MAX(name) as name')
            ->selectRaw('MAX(ip_number) as ip_number')
            ->groupBy('id');

        $query = DB::table('income_entries')
            ->leftJoinSub($patients, 'patient_records', function ($join) {
                $join->on('income_entries.patient_id', '=', 'patient_records.id');
            })
            ->select(
                'income_entries.id',
                'income_entries.patient_id',
                'income_entries.income_date',
                'income_entries.amount',
                'income_entries.payment_mode',
                DB::raw("COALESCE(patient_records.name, 'Hand Cash') as patient_name"),
                'patient_records.ip_number'
            );

        if ($request->filled('from_date')) {
            $query->whereDate('income_entries.income_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('income_entries.income_date', '<=', $request->input('to_date'));
        }

        if ($request->filled('payment_mode') && $request->input('payment_mode') !== 'both') {
            $query->where('income_entries.payment_mode', $request->input('payment_mode'));
        }

        $totalAmount = (clone $query)->sum('income_entries.amount');
        $incomes = $query->orderByDesc('income_entries.income_date')
            ->orderByDesc('income_entries.id')
            ->paginate(15)
            ->withQueryString();

        return view('income.reports', [
            'incomes' => $incomes,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function balanceReport(Request $request)
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'payment_mode' => ['nullable', 'in:both,Cash,GPay,Hand Cash'],
        ]);

        $incomeQuery = DB::table('income_entries')
            ->select('income_date as report_date', 'payment_mode')
            ->selectRaw('SUM(amount) as total_income')
            ->selectRaw('0 as total_expenses')
            ->groupBy('income_date', 'payment_mode');

        $expenseQuery = DB::table('new_expense_entries')
            ->select('expense_date as report_date', 'payment_mode')
            ->selectRaw('0 as total_income')
            ->selectRaw('SUM(amount) as total_expenses')
            ->groupBy('expense_date', 'payment_mode');

        if (!empty($filters['from_date'])) {
            $incomeQuery->where('income_date', '>=', $filters['from_date']);
            $expenseQuery->where('expense_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $incomeQuery->where('income_date', '<=', $filters['to_date']);
            $expenseQuery->where('expense_date', '<=', $filters['to_date']);
        }

        if (!empty($filters['payment_mode']) && $filters['payment_mode'] !== 'both') {
            $incomeQuery->where('payment_mode', $filters['payment_mode']);
            $expenseQuery->where('payment_mode', $filters['payment_mode']);
        }

        $rows = DB::query()
            ->fromSub($incomeQuery->unionAll($expenseQuery), 'transactions')
            ->select('report_date', 'payment_mode')
            ->selectRaw('SUM(total_income) as total_income')
            ->selectRaw('SUM(total_expenses) as total_expenses')
            ->groupBy('report_date', 'payment_mode')
            ->orderByDesc('report_date')
            ->orderBy('payment_mode')
            ->get()
            ->map(function ($row) {
                $row->balance = (float) $row->total_income - (float) $row->total_expenses;

                return $row;
            });

        return view('income.balance_report', [
            'rows' => $rows,
            'totalIncome' => $rows->sum('total_income'),
            'totalExpenses' => $rows->sum('total_expenses'),
            'totalBalance' => $rows->sum('balance'),
        ]);
    }

    private function patientOptions()
    {
        return DB::table('patients')
            ->select('id')
            ->selectRaw('MAX(name) as name')
            ->selectRaw('MAX(ip_number) as ip_number')
            ->groupBy('id')
            ->orderBy('name')
            ->get();
    }
}
