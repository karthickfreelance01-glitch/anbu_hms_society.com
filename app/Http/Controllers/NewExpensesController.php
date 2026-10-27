<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewExpensesController extends Controller
{
    public function create()
    {
        $categories = DB::table('expenses')
            ->select('id')
            ->selectRaw('MAX(expense_name) as expense_name')
            ->groupBy('id')
            ->orderBy('expense_name')
            ->get();

        $submissionKey = (string) Str::uuid();

        return view('new_expenses.create', compact('categories', 'submissionKey'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash',
            'submission_key' => 'required|uuid',
        ]);

        DB::table('new_expense_entries')->upsert([
            [
                'expense_id' => $validated['expense_id'],
                'expense_date' => $validated['date'],
                'amount' => $validated['amount'],
                'payment_mode' => $validated['payment_mode'],
                'submission_key' => $validated['submission_key'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['submission_key'], ['submission_key']);

        return redirect()->route('new-expenses.reports')->with('success', 'Expense recorded successfully.');
    }

    public function edit($id)
    {
        $expense = DB::table('new_expense_entries')->where('id', $id)->first();
        abort_unless($expense, 404);

        $categories = DB::table('expenses')
            ->select('id')
            ->selectRaw('MAX(expense_name) as expense_name')
            ->groupBy('id')
            ->orderBy('expense_name')
            ->get();

        return view('new_expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:GPay,Cash',
        ]);

        $query = DB::table('new_expense_entries')->where('id', $id);
        abort_unless((clone $query)->exists(), 404);

        $query->update([
            'expense_id' => $validated['expense_id'],
            'expense_date' => $validated['date'],
            'amount' => $validated['amount'],
            'payment_mode' => $validated['payment_mode'],
            'updated_at' => now(),
        ]);

        return redirect()->route('new-expenses.reports')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = DB::table('new_expense_entries')->where('id', $id)->delete();
        abort_unless($deleted, 404);

        return redirect()->route('new-expenses.reports')->with('success', 'Expense deleted successfully.');
    }

    public function reports(Request $request)
    {
        $categories = DB::table('expenses')
            ->select('id')
            ->selectRaw('MAX(expense_name) as expense_name')
            ->groupBy('id');

        $query = DB::table('new_expense_entries')
            ->joinSub($categories, 'expense_categories', function ($join) {
                $join->on('new_expense_entries.expense_id', '=', 'expense_categories.id');
            })
            ->select(
                'new_expense_entries.id',
                'new_expense_entries.expense_date',
                'new_expense_entries.amount',
                'new_expense_entries.payment_mode',
                'expense_categories.expense_name'
            );

        if ($request->filled('from_date')) {
            $query->whereDate('new_expense_entries.expense_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('new_expense_entries.expense_date', '<=', $request->input('to_date'));
        }

        if ($request->filled('payment_mode') && !in_array($request->input('payment_mode'), ['both', 'all', 'All'], true)) {
            $mode = $request->input('payment_mode');
            if (in_array($mode, ['Cash', 'Hand Cash'], true)) {
                $query->whereIn('new_expense_entries.payment_mode', ['Cash', 'Hand Cash', 'Cash / Hand Cash']);
            } else {
                $query->where('new_expense_entries.payment_mode', $mode);
            }
        }

        $totalAmount = (clone $query)->sum('new_expense_entries.amount');
        $expenses = $query->orderByDesc('new_expense_entries.expense_date')
            ->orderByDesc('new_expense_entries.id')
            ->paginate(15)
            ->withQueryString();

        return view('new_expenses.reports', [
            'expenses' => $expenses,
            'totalAmount' => $totalAmount,
        ]);
    }
}
