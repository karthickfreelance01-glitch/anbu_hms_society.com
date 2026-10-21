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
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash',
        ]);

        DB::table('income_entries')->insert([
            'patient_id' => $validated['patient_id'],
            'income_date' => $validated['date'],
            'amount' => $validated['amount'],
            'payment_mode' => $validated['payment_mode'],
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
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash',
        ]);

        $query = DB::table('income_entries')->where('id', $id);
        abort_unless((clone $query)->exists(), 404);

        $query->update([
            'patient_id' => $validated['patient_id'],
            'income_date' => $validated['date'],
            'amount' => $validated['amount'],
            'payment_mode' => $validated['payment_mode'],
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
            ->joinSub($patients, 'patient_records', function ($join) {
                $join->on('income_entries.patient_id', '=', 'patient_records.id');
            })
            ->select(
                'income_entries.id',
                'income_entries.income_date',
                'income_entries.amount',
                'income_entries.payment_mode',
                'patient_records.name as patient_name',
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
