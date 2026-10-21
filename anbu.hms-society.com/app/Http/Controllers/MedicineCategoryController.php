<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('medicine_categories')->get();
        return view('masters.medicine', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_category' => 'required|string|max:255',
        ]);

        DB::table('medicine_categories')->insert([
            'medicine_category' => $request->medicine_category,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $category = DB::table('medicine_categories')->where('id', $id)->first();
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_category' => 'required|string|max:255',
        ]);

        DB::table('medicine_categories')->where('id', $id)->update([
            'medicine_category' => $request->medicine_category,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table('medicine_categories')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Medicine Category deleted successfully');
    }
}
