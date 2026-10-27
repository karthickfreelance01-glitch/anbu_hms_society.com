<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncomeController extends Controller
{
    public function create()
    {
        $patients = $this->patientOptions();
        $submissionKey = (string) Str::uuid();

        return view('income.create', compact('patients', 'submissionKey'));
    }

    public function store(Request $request)
    {
        $rawPatient = $request->input('patient_id');
        $isHandCash = in_array($rawPatient, ['hand_cash', 'none', 'direct_cash'], true) || empty($rawPatient);

        $validated = $request->validate([
            'patient_id' => $isHandCash ? 'nullable|in:hand_cash,none,direct_cash,' : 'required|exists:patients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash,Hand Cash,Cash / Hand Cash',
            'submission_key' => 'nullable|uuid',
        ]);

        $paymentMode = $validated['payment_mode'] === 'GPay' ? 'GPay' : 'Cash';
        $patientId = $isHandCash ? null : (int) $validated['patient_id'];
        $submissionKey = $request->input('submission_key');

        if ($submissionKey) {
            DB::table('income_entries')->upsert([
                [
                    'patient_id' => $patientId,
                    'income_date' => $validated['date'],
                    'amount' => $validated['amount'],
                    'payment_mode' => $paymentMode,
                    'submission_key' => $submissionKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ], ['submission_key'], ['patient_id', 'income_date', 'amount', 'payment_mode', 'updated_at']);
        } else {
            DB::table('income_entries')->insert([
                'patient_id' => $patientId,
                'income_date' => $validated['date'],
                'amount' => $validated['amount'],
                'payment_mode' => $paymentMode,
                'submission_key' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

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
        $rawPatient = $request->input('patient_id');
        $isHandCash = in_array($rawPatient, ['hand_cash', 'none', 'direct_cash'], true) || empty($rawPatient);

        $validated = $request->validate([
            'patient_id' => $isHandCash ? 'nullable|in:hand_cash,none,direct_cash,' : 'required|exists:patients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash,Hand Cash,Cash / Hand Cash',
        ]);

        $paymentMode = $validated['payment_mode'] === 'GPay' ? 'GPay' : 'Cash';
        $patientId = $isHandCash ? null : (int) $validated['patient_id'];

        $query = DB::table('income_entries')->where('id', $id);
        abort_unless((clone $query)->exists(), 404);

        $query->update([
            'patient_id' => $patientId,
            'income_date' => $validated['date'],
            'amount' => $validated['amount'],
            'payment_mode' => $paymentMode,
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
                DB::raw("COALESCE(patient_records.name, 'Hand Cash / Cash Collection') as patient_name"),
                'patient_records.ip_number'
            );

        if ($request->filled('from_date')) {
            $query->whereDate('income_entries.income_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('income_entries.income_date', '<=', $request->input('to_date'));
        }

        if ($request->filled('payment_mode') && !in_array($request->input('payment_mode'), ['both', 'all', 'All'], true)) {
            $mode = $request->input('payment_mode');
            if (in_array($mode, ['Cash', 'Hand Cash'], true)) {
                $query->whereIn('income_entries.payment_mode', ['Cash', 'Hand Cash', 'Cash / Hand Cash']);
            } else {
                $query->where('income_entries.payment_mode', $mode);
            }
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
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'payment_mode' => ['nullable', 'string', 'in:all,All,both,Both,Cash,cash,GPay,gpay,Hand Cash'],
        ]);

        $fromDate = $request->filled('from_date') ? $request->input('from_date') : null;
        $toDate = $request->filled('to_date') ? $request->input('to_date') : null;

        $rawMode = $request->input('payment_mode', 'All');
        if (in_array(strtolower((string) $rawMode), ['all', 'both', ''], true)) {
            $selectedPaymentMode = 'All';
        } elseif (in_array(strtolower((string) $rawMode), ['cash', 'hand cash'], true)) {
            $selectedPaymentMode = 'Cash';
        } elseif (strtolower((string) $rawMode) === 'gpay') {
            $selectedPaymentMode = 'GPay';
        } else {
            $selectedPaymentMode = 'All';
        }

        // 1. Calculate Historical Opening Balance (transactions strictly before from_date)
        $openingCash = 0.0;
        $openingGPay = 0.0;

        if ($fromDate) {
            $histCashIncome = (float) DB::table('income_entries')
                ->where('income_date', '<', $fromDate)
                ->whereIn('payment_mode', ['Cash', 'Hand Cash', 'Cash / Hand Cash'])
                ->sum('amount');

            $histCashExpense = (float) DB::table('new_expense_entries')
                ->where('expense_date', '<', $fromDate)
                ->whereIn('payment_mode', ['Cash', 'Hand Cash', 'Cash / Hand Cash'])
                ->sum('amount');

            $histGPayIncome = (float) DB::table('income_entries')
                ->where('income_date', '<', $fromDate)
                ->where('payment_mode', 'GPay')
                ->sum('amount');

            $histGPayExpense = (float) DB::table('new_expense_entries')
                ->where('expense_date', '<', $fromDate)
                ->where('payment_mode', 'GPay')
                ->sum('amount');

            $openingCash = $histCashIncome - $histCashExpense;
            $openingGPay = $histGPayIncome - $histGPayExpense;
        }

        if ($selectedPaymentMode === 'Cash') {
            $openingGPay = 0.0;
            $openingTotal = $openingCash;
        } elseif ($selectedPaymentMode === 'GPay') {
            $openingCash = 0.0;
            $openingTotal = $openingGPay;
        } else {
            $openingTotal = $openingCash + $openingGPay;
        }

        // 2. Query transactions within the period using efficient database aggregation
        $incomeAggQuery = DB::table('income_entries')
            ->select('income_date as tx_date')
            ->selectRaw("SUM(CASE WHEN payment_mode IN ('Cash', 'Hand Cash', 'Cash / Hand Cash') THEN amount ELSE 0 END) as cash_income")
            ->selectRaw("SUM(CASE WHEN payment_mode = 'GPay' THEN amount ELSE 0 END) as gpay_income")
            ->selectRaw("SUM(amount) as total_income");

        if ($fromDate) {
            $incomeAggQuery->where('income_date', '>=', $fromDate);
        }
        if ($toDate) {
            $incomeAggQuery->where('income_date', '<=', $toDate);
        }
        $incomeByDate = $incomeAggQuery->groupBy('income_date')->get()->keyBy('tx_date');

        $expenseAggQuery = DB::table('new_expense_entries')
            ->select('expense_date as tx_date')
            ->selectRaw("SUM(CASE WHEN payment_mode IN ('Cash', 'Hand Cash', 'Cash / Hand Cash') THEN amount ELSE 0 END) as cash_expense")
            ->selectRaw("SUM(CASE WHEN payment_mode = 'GPay' THEN amount ELSE 0 END) as gpay_expense")
            ->selectRaw("SUM(amount) as total_expense");

        if ($fromDate) {
            $expenseAggQuery->where('expense_date', '>=', $fromDate);
        }
        if ($toDate) {
            $expenseAggQuery->where('expense_date', '<=', $toDate);
        }
        $expensesByDate = $expenseAggQuery->groupBy('expense_date')->get()->keyBy('tx_date');

        // 3. Merge all active transaction dates in chronological order
        $allDates = $incomeByDate->keys()
            ->merge($expensesByDate->keys())
            ->unique()
            ->sort()
            ->values();

        $runningCash = $openingCash;
        $runningGPay = $openingGPay;

        $dailyRecords = [];

        foreach ($allDates as $dateStr) {
            $inc = $incomeByDate->get($dateStr);
            $exp = $expensesByDate->get($dateStr);

            $rawCashInc = $inc ? (float) $inc->cash_income : 0.0;
            $rawGPayInc = $inc ? (float) $inc->gpay_income : 0.0;

            $rawCashExp = $exp ? (float) $exp->cash_expense : 0.0;
            $rawGPayExp = $exp ? (float) $exp->gpay_expense : 0.0;

            if ($selectedPaymentMode === 'Cash') {
                $cashInc = $rawCashInc;
                $cashExp = $rawCashExp;
                $gpayInc = 0.0;
                $gpayExp = 0.0;
            } elseif ($selectedPaymentMode === 'GPay') {
                $cashInc = 0.0;
                $cashExp = 0.0;
                $gpayInc = $rawGPayInc;
                $gpayExp = $rawGPayExp;
            } else {
                $cashInc = $rawCashInc;
                $cashExp = $rawCashExp;
                $gpayInc = $rawGPayInc;
                $gpayExp = $rawGPayExp;
            }

            // Transaction Daily Balances (Section 4)
            $cashClosing = $cashInc - $cashExp;
            $gpayClosing = $gpayInc - $gpayExp;
            $totalInc = $cashInc + $gpayInc;
            $totalExp = $cashExp + $gpayExp;
            $dailyNet = $totalInc - $totalExp;

            // Running Cumulative Balances (Section 6)
            $dayCashOpening = $runningCash;
            $dayGPayOpening = $runningGPay;
            $dayTotalOpening = $dayCashOpening + $dayGPayOpening;

            $dayCashRunningClosing = $dayCashOpening + $cashClosing;
            $dayGPayRunningClosing = $dayGPayOpening + $gpayClosing;
            $dayTotalRunningClosing = $dayTotalOpening + $dailyNet;

            // Advance running balances
            $runningCash = $dayCashRunningClosing;
            $runningGPay = $dayGPayRunningClosing;

            $dailyRecords[] = (object) [
                'date' => $dateStr,
                // Section 4 Daily Columns
                'cash_income' => $cashInc,
                'cash_expenses' => $cashExp,
                'cash_closing' => $cashClosing,
                'gpay_income' => $gpayInc,
                'gpay_expenses' => $gpayExp,
                'gpay_closing' => $gpayClosing,
                'total_income' => $totalInc,
                'total_expenses' => $totalExp,
                'daily_net' => $dailyNet,

                // Section 6 Running Balance Columns
                'cash_opening' => $dayCashOpening,
                'cash_running_closing' => $dayCashRunningClosing,
                'gpay_opening' => $dayGPayOpening,
                'gpay_running_closing' => $dayGPayRunningClosing,
                'total_opening' => $dayTotalOpening,
                'total_running_closing' => $dayTotalRunningClosing,
            ];
        }

        // Summary totals across period
        $totalCashIncome = collect($dailyRecords)->sum('cash_income');
        $totalCashExpenses = collect($dailyRecords)->sum('cash_expenses');
        $totalCashClosing = $totalCashIncome - $totalCashExpenses;

        $totalGPayIncome = collect($dailyRecords)->sum('gpay_income');
        $totalGPayExpenses = collect($dailyRecords)->sum('gpay_expenses');
        $totalGPayClosing = $totalGPayIncome - $totalGPayExpenses;

        $periodTotalIncome = collect($dailyRecords)->sum('total_income');
        $periodTotalExpenses = collect($dailyRecords)->sum('total_expenses');
        $periodNetBalance = $periodTotalIncome - $periodTotalExpenses;

        $finalClosingCash = $runningCash;
        $finalClosingGPay = $runningGPay;
        $finalClosingTotal = $runningCash + $runningGPay;

        // Sort by date descending (newest first) for display
        $rows = collect($dailyRecords)->sortByDesc('date')->values();

        return view('income.balance_report', [
            'rows' => $rows,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedPaymentMode' => $selectedPaymentMode,

            // Section 3: Summary Cards
            'totalIncome' => $periodTotalIncome,
            'totalExpenses' => $periodTotalExpenses,
            'netBalance' => $periodNetBalance,
            'cashIncome' => $totalCashIncome,
            'cashExpenses' => $totalCashExpenses,
            'cashClosingBalance' => $totalCashClosing,
            'gpayIncome' => $totalGPayIncome,
            'gpayExpenses' => $totalGPayExpenses,
            'gpayClosingBalance' => $totalGPayClosing,

            // Section 6: Opening & Cumulative Carry-Forward
            'openingCash' => $openingCash,
            'openingGPay' => $openingGPay,
            'openingTotal' => $openingTotal,
            'finalClosingCash' => $finalClosingCash,
            'finalClosingGPay' => $finalClosingGPay,
            'finalClosingTotal' => $finalClosingTotal,
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
