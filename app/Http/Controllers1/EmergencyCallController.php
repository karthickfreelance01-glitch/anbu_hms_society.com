<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class EmergencyCallController extends Controller
{
    public function index()
    {
        $emergencies = DB::table('emergency_callregister')
            ->join('patients', 'emergency_callregister.patient_id', '=', 'patients.id')
            ->select('emergency_callregister.*', 'patients.reg_no', 'patients.name as patient_name')
            ->get();

        return view('emergencycallregister.index', compact('emergencies'));
    }

    public function create()
    {
        $patients = DB::table('patients')->get();
        return view('emergencycallregister.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'date' => 'required|date',
            'illness' => 'nullable|string|max:255',
            'doctor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        DB::table('emergency_callregister')->insert([
            'patient_id' => $request->patient_id,
            'date' => $request->date,
            'illness' => $request->illness,
            'doctor' => $request->doctor,
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Emergency hospitalization added successfully!']);
    }

    public function edit($id)
    {
        $record = DB::table('emergency_callregister')->where('id', $id)->first();
        $patients = DB::table('patients')->get();
        return view('emergencycallregister.edit', compact('record', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required',
            'date' => 'required|date',
            'illness' => 'nullable|string|max:255',
            'doctor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        DB::table('emergency_callregister')->where('id', $id)->update([
            'patient_id' => $request->patient_id,
            'date' => $request->date,
            'illness' => $request->illness,
            'doctor' => $request->doctor,
            'remarks' => $request->remarks,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Record updated successfully!']);
    }

    public function destroy($id)
    {
        DB::table('emergency_callregister')->where('id', $id)->delete();
        return response()->json(['message' => 'Record deleted successfully!']);
    }
}
