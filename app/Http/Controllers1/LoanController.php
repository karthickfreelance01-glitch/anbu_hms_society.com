<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $loans = DB::table('loans')
            ->leftJoin('payment_categories', 'loans.hospital_payment_category', '=', 'payment_categories.id')
            ->select(
                'loans.id',
                'loans.loan_amount',
                'loans.bank_name',
                'loans.loan_date',
                'payment_categories.payment_category'
            )
            ->orderBy('loans.loan_date', 'desc')
            ->get();

        $payment_categories = DB::table('payment_categories')->get();

        return view('loan.index', compact('loans', 'payment_categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loan_amount' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'loan_date' => 'required|date',
            'hospital_account' => 'required|integer',
        ]);

        try {
            $id = DB::table('loans')->insertGetId([
                'loan_amount' => $request->loan_amount,
                'bank_name' => $request->bank_name,
                'loan_date' => $request->loan_date,
                'hospital_payment_category' => $request->hospital_account,
                
            ]);

            $loan = DB::table('loans')->where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Loan added successfully!',
                'loan' => $loan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $loan = DB::table('loans')->where('id', $id)->first();
        if (!$loan) {
            return response()->json(['success'=>false,'message'=>'Loan not found!'],404);
        }
        return response()->json($loan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'loan_amount' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'loan_date' => 'required|date',
            'hospital_account' => 'required|integer',
        ]);

        try {
            DB::table('loans')->where('id', $id)->update([
                'loan_amount' => $request->loan_amount,
                'bank_name' => $request->bank_name,
                'loan_date' => $request->loan_date,
                'hospital_payment_category' => $request->hospital_account,
               
            ]);

            return response()->json(['success'=>true,'message'=>'Loan updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>'Error: '.$e->getMessage()],500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('loans')->where('id',$id)->delete();
            if(!$deleted){
                return response()->json(['success'=>false,'message'=>'Loan not found!'],404);
            }
            return response()->json(['success'=>true,'message'=>'Loan deleted successfully!']);
        } catch (\Exception $e){
            return response()->json(['success'=>false,'message'=>'Error: '.$e->getMessage()],500);
        }
    }
}
