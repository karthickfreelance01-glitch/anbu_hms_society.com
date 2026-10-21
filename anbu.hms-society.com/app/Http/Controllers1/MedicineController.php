<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    // Show all medicines
    public function index()
    {
        $medicines = DB::table('medicines')->orderBy('id','desc')->get();
        return view('masters.medicine', compact('medicines'));
    }

    // Store new medicine
    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        DB::table('medicines')->insert([
            'medicine_name' => $request->medicine_name,
            'brand' => $request->brand,
            'category' => $request->category,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success'=>true]);
    }

    // Get medicine data for edit
    public function edit($id)
    {
        $medicine = DB::table('medicines')->where('id', $id)->first();
        return response()->json($medicine);
    }

    // Update medicine
    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        DB::table('medicines')->where('id', $id)->update([
            'medicine_name' => $request->medicine_name,
            'brand' => $request->brand,
            'category' => $request->category,
            'updated_at' => now(),
        ]);

        return response()->json(['success'=>true]);
    }

    // Delete medicine
    public function destroy($id)
    {
        DB::table('medicines')->where('id', $id)->delete();
        return redirect()->back()->with('success','Medicine deleted successfully!');
    }
}
