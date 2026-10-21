<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpensesController extends Controller
{
    
     public function create()
    {
        $expenses = DB::table('expenses')->get();

        return view('expenses.create', compact('expenses'));
    }

    // Store Expense
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'expense_category' => 'required|exists:expensescategory,id',
    //         'expenses_id' => 'required|exists:expenses,id',
    //         'amount' => 'required|numeric',
    //         'expense_date' => 'required|date',
    //     ]);

    //     DB::table('expenses')->insert([
    //         'expense_category' => $request->expense_category,
    //         'expenses_id' => $request->expense_id,
    //         'amount' => $request->amount,
    //         'expense_date' => $request->expense_date,
    //         'payment_hospital_account' => $request->payment_hospital_account,
    //         'payment_mode' => $request->payment_mode,
    //         'notes' => $request->notes,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     return response()->json(['message' => 'Expense saved successfully']);
    // }

    // Get expenses based on category (for dependent dropdown)
  

    public function store(Request $request)
{
    
    // Insert
    $expenseId = DB::table('expenses_details')->insertGetId([
        
        'expenses_id'               => $request->expense_id,
        'accounttype'               => $request->accounttype,
        'amount'                    => $request->amount,
        'expense_date'              => $request->expense_date,  
        'notes'                     => $request->notes ?? null,
        'created_at'                => now(),
        'updated_at'                => now(),
    ]);

    return response()->json([
        'message' => 'Expense added successfully!',
        'expense_id' => $expenseId
    ]);
}


public function index()
{
    $expenses = DB::table('expenses_details')
        ->join('expenses', 'expenses_details.expenses_id', '=', 'expenses.id')       
        ->select(
            'expenses_details.*',
            'expenses.expense_name'
        )
        ->orderBy('expenses_details.expense_date', 'desc')
        ->get();

    return view('expenses.index', compact('expenses'));
}


 public function reports(Request $request)
    {
        // Base Query
        $query = DB::table('expenses_details')
            ->join('expenses', 'expenses_details.expenses_id', '=', 'expenses.id')
            ->select(
                'expenses_details.*',
                'expenses.expense_name'
            );

        // Apply Filters
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('expenses_details.expense_date', [$request->from_date, $request->to_date]);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('expenses.id', $request->sub_category_id);
        }

        if ($request->filled('accounttype')) {
            $query->where('expenses_details.accounttype', $request->accounttype);
        }

        // Fetch Filtered Results
        $expenses = $query->orderBy('expenses_details.expense_date', 'desc')->get();

        // Group by Expense Name
        $groupedExpenses = $expenses->groupBy('expense_name');

        // Dropdown Data
        $subcategories = DB::table('expenses')->pluck('expense_name', 'id');

        // Overall Total
        $totalAmount = $expenses->sum('amount');

        // Return View
        return view('expenses.reports', compact(
            'expenses',
            'groupedExpenses',
            'subcategories',
            'totalAmount'
        ));
    }
    public function edit($id)
{
    // Fetch the expense details with joins
    $expense = DB::table('expenses_details')
        ->join('expenses', 'expenses_details.expenses_id', '=', 'expenses.id')
        ->select(
            'expenses_details.*',
            'expenses.expense_name'
        )
        ->where('expenses_details.id', $id)
        ->first();

    if (!$expense) {
        return redirect()->route('expenses.index')->with('error', 'Expense not found.');
    }

    // Fetch all main expense categories
    $expensecat = DB::table('expenses')->get();

    return view('expenses.edit', compact('expense', 'expensecat'));
}

// Update expense
  public function update(Request $request, $id)
{
    // Validate the request
   

    // Update the expense
    DB::table('expenses_details')->where('id', $id)->update([
       
        'expenses_id' => $request->expenses_id,                    // Subcategory
        'amount' => $request->amount,
        'expense_date' => $request->expense_date,        
        'notes' => $request->notes ?? null,
        'updated_at' => now(),
    ]);

    return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
}

    // Delete expense
    public function destroy($id)
    {
        DB::table('expenses_details')->where('id', $id)->delete();
        return response()->json(['message'=>'Expense deleted successfully!']);
    }



}
