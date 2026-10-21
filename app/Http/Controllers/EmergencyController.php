<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmergencyController extends Controller
{
    public function index()
    {
        $emergencies = DB::table('emergency_hospitalizations')
            ->join('patients', 'emergency_hospitalizations.patient_id', '=', 'patients.id')
            ->select('emergency_hospitalizations.*', 'patients.reg_no', 'patients.name as patient_name')
            ->get();

        return view('emergency.index', compact('emergencies'));
    }

    public function create()
    {
        $patients = DB::table('patients')->get();
        return view('emergency.create', compact('patients'));
    }

   public function store(Request $request)
{
    DB::table('emergency_hospitalizations')->insert([
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
        $patients = DB::table('patients')->get();
        $record = DB::table('emergency_hospitalizations')->where('id', $id)->first();
        return view('emergency.edit', compact('record', 'patients'));
    }

    public function update(Request $request, $id)
    {
        DB::table('emergency_hospitalizations')->where('id', $id)->update([
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
        DB::table('emergency_hospitalizations')->where('id', $id)->delete();
        return response()->json(['message' => 'Record deleted successfully!']);
    }

}
