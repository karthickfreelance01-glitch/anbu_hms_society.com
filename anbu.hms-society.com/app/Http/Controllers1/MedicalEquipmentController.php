<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalEquipmentController extends Controller
{
    public function index()
    {
        $equipments = DB::table('medical_equipments')->orderBy('id', 'desc')->get();
        return view('medical_equipments.index', compact('equipments'));
    }

    public function create()
    {
        return view('medical_equipments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'repair' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::table('medical_equipments')->insert([
            'equipment_name' => $request->equipment_name,
            'stock' => $request->stock,
            'repair' => $request->repair,
            'purchase_date' => $request->purchase_date,
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $equipment = DB::table('medical_equipments')->find($id);
        return view('medical_equipments.edit', compact('equipment'));
    }

    public function update(Request $request, $id)
    {
       
        //dd($request->repair);
        DB::table('medical_equipments')->where('id', $id)->update([
            'equipment_name' => $request->equipment_name,
            'stock' => $request->stock,
            'repair' => $request->repair,
            'purchase_date' => $request->purchase_date,
            'remarks' => $request->remarks,
            'updated_at' => now(),
        ]);

        return redirect('equipments');
    }

    public function destroy($id)
    {
        DB::table('medical_equipments')->where('id', $id)->delete();
        return redirect('equipments');
    }
}
