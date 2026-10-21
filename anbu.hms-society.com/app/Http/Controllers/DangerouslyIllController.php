<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DangerouslyIllController extends Controller
{
    // 🩺 Show all records
    public function index()
    {
        $records = DB::table('dangerously_ill')
            ->join('patients', 'dangerously_ill.patient_id', '=', 'patients.id')
            ->select('dangerously_ill.*', 'patients.ip_number')
            ->orderByDesc('dangerously_ill.id')
            ->get();

        return view('dangerously_ill.index', compact('records'));
    }

    // ➕ Create form
    public function create()
    {
        $patients = DB::table('patients')->select('id', 'name', 'ip_number')->get();
        return view('dangerously_ill.create', compact('patients'));
    }

    // 💾 Store record
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'before_admission_condition' => 'required|string',
            'treatment_given' => 'required|string',
        ]);

        $patient = DB::table('patients')->where('id', $request->patient_id)->first();

        if (!$patient) {
            return redirect()->back()->with('msg', 'Invalid patient selected.');
        }

        DB::table('dangerously_ill')->insert([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'age' => $patient->age,
            'admission_date' => $patient->date_of_admission,
            'before_admission_condition' => $request->before_admission_condition,
            'treatment_given' => $request->treatment_given,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('dangerously-ill')->with('success', 'Record added successfully!');
    }

    // ✏️ Edit form
    public function edit($id)
    {
        $record = DB::table('dangerously_ill')->where('id', $id)->first();

        if (!$record) {
            return redirect('dangerously-ill')->with('msg', 'Record not found.');
        }

        $patients = DB::table('patients')->select('id', 'name', 'ip_number')->get();

        return view('dangerously_ill.edit', compact('record', 'patients'));
    }

    // 🔄 Update record
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'before_admission_condition' => 'required|string',
            'treatment_given' => 'required|string',
        ]);

        $patient = DB::table('patients')->where('id', $request->patient_id)->first();

        if (!$patient) {
            return redirect()->back()->with('msg', 'Invalid patient selected.');
        }

        DB::table('dangerously_ill')->where('id', $id)->update([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'age' => $patient->age,
            'admission_date' => $patient->date_of_admission,
            'before_admission_condition' => $request->before_admission_condition,
            'treatment_given' => $request->treatment_given,
            'updated_at' => now(),
        ]);

        return redirect('dangerously-ill')->with('success', 'Record updated successfully!');
    }

    // 🗑️ Delete record
    public function destroy($id)
    {
        $record = DB::table('dangerously_ill')->where('id', $id)->first();

        if ($record) {
            DB::table('dangerously_ill')->where('id', $id)->delete();
            return redirect('dangerously-ill')->with('success', 'Record deleted successfully!');
        }

        return redirect('dangerously-ill')->with('msg', 'Record not found.');
    }

    // 🔍 Get Patient Info (for AJAX if needed)
    public function getPatient($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        return response()->json($patient);
    }
}
