<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InjuryController extends Controller
{
    // 🩺 Show all injury records
    public function index()
    {
        $injuries = DB::table('injuries')
            ->join('patients', 'injuries.patient_id', '=', 'patients.id')
            ->select('injuries.*', 'patients.ip_number')
            ->orderByDesc('injuries.id')
            ->get();

        return view('injury.index', compact('injuries'));
    }

    // ➕ Show create form
    public function create()
    {
        $patients = DB::table('patients')->select('id', 'name', 'ip_number')->get();
        return view('injury.create', compact('patients'));
    }

    // 💾 Store new record
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'injury_date' => 'required|date',
            'injury_time' => 'required',
            'incident' => 'required|string',
            'physician' => 'required|string'
        ]);

        $patient = DB::table('patients')->where('id', $request->patient_id)->first();

        if (!$patient) {
            return redirect()->back()->with('msg', 'Invalid patient selected.');
        }

        DB::table('injuries')->insert([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'age' => $patient->age,
            'admission_date' => $patient->date_of_admission,
            'injury_date' => $request->injury_date,
            'injury_time' => $request->injury_time,
            'incident' => $request->incident,
            'physician' => $request->physician,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('injuries')->with('success', 'Injury record added successfully!');
    }

    // ✏️ Show edit form
    public function edit($id)
    {
        $injury = DB::table('injuries')->where('id', $id)->first();

        if (!$injury) {
            return redirect('injuries')->with('msg', 'Injury record not found.');
        }

        $patients = DB::table('patients')->select('id', 'name', 'ip_number')->get();

        return view('injury.edit', compact('injury', 'patients'));
    }

    // 🔄 Update existing record
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'injury_date' => 'required|date',
            'injury_time' => 'required',
            'incident' => 'required|string',
            'physician' => 'required|string'
        ]);

        $patient = DB::table('patients')->where('id', $request->patient_id)->first();

        if (!$patient) {
            return redirect()->back()->with('msg', 'Invalid patient selected.');
        }

        DB::table('injuries')->where('id', $id)->update([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'age' => $patient->age,
            'admission_date' => $patient->date_of_admission,
            'injury_date' => $request->injury_date,
            'injury_time' => $request->injury_time,
            'incident' => $request->incident,
            'physician' => $request->physician,
            'updated_at' => now(),
        ]);

        return redirect('injuries')->with('success', 'Injury record updated successfully!');
    }

    // 🗑️ Delete record
    public function destroy($id)
    {
        $injury = DB::table('injuries')->where('id', $id)->first();

        if ($injury) {
            DB::table('injuries')->where('id', $id)->delete();
            return redirect('injuries')->with('success', 'Injury record deleted successfully!');
        }

        return redirect('injuries')->with('msg', 'Injury record not found.');
    }

    public function getPatientDetails($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        return response()->json($patient);
    }
}
