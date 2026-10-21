<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesmasterController extends Controller
{
    // ✅ Add new expense (AJAX)
    public function addexpenses(Request $request)
    {
        $request->validate([
            'expense_name' => 'required|string|max:255',
        ]);

        DB::table('expenses')->insert([
            'expense_name' => $request->expense_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Expense added successfully.']);
    }

    // ✅ View all expenses
    public function view()
    {
        $expenses = DB::table('expenses')->orderBy('id', 'desc')->get();
        return view('masters.expense', compact('expenses'));
    }

    // ✅ Fetch single expense for edit (AJAX)
    public function expenses_edit($id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Return as JSON for your edit modal
        return response()->json($expense);
    }

    // ✅ Update expense (AJAX)
    public function expenses_update(Request $request, $id)
    {
        $request->validate([
            'expense_name' => 'required|string|max:255',
        ]);

        $updated = DB::table('expenses')
            ->where('id', $id)
            ->update([
                'expense_name' => $request->expense_name,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return response()->json(['message' => 'Update failed or no changes made.'], 400);
        }

        return response()->json(['message' => 'Expense updated successfully.']);
    }

    // ✅ Delete expense
    public function expenses_delete($id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();

        if (!$expense) {
            return redirect()->back()->with('error', 'Expense not found!');
        }

        DB::table('expenses')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully!');
    }
}
