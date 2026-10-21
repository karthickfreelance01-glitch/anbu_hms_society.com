<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientHearingController extends Controller
{
    // Hearing list page
    public function index()
    {
        $hearings = DB::table('patient_hearings')
            ->join('patients', 'patient_hearings.patient_id', '=', 'patients.id')
            ->select(
                'patient_hearings.*',
                'patients.name as patient_name',
                'patients.ip_number'
            )
            ->orderByDesc('patient_hearings.id')
            ->get();

        return view('patient_hearings.index', compact('hearings'));
    }

    // Show create form
    public function create()
    {
        $patients = DB::table('patients')->select('id', 'name', 'ip_number', 'date_of_admission')->get();
        return view('patient_hearings.create', compact('patients'));
    }

    // Store new hearing record
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'fir_csr' => 'required|string|max:255',
            'date_of_hearing' => 'required|date',
        ]);

        DB::table('patient_hearings')->insert([
            'patient_id' => $request->patient_id,
            'date_of_admission' => $request->date_of_admission,
            'fir_csr' => $request->fir_csr,
            'date_of_hearing' => $request->date_of_hearing,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('hearings.index')->with('success', 'Hearing details saved successfully.');
    }

    // Edit form
    public function edit($id)
    {
        $hearing = DB::table('patient_hearings')->where('id', $id)->first();
        $patients = DB::table('patients')->select('id', 'name', 'ip_number')->get();
        return view('patient_hearings.edit', compact('hearing', 'patients'));
    }

    // Update record
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required',
            'fir_csr' => 'required|string|max:255',
            'date_of_hearing' => 'required|date',
        ]);

        DB::table('patient_hearings')
            ->where('id', $id)
            ->update([
                'patient_id' => $request->patient_id,
                'date_of_admission' => $request->date_of_admission,
                'fir_csr' => $request->fir_csr,
                'date_of_hearing' => $request->date_of_hearing,
                'updated_at' => now(),
            ]);

        return redirect()->route('hearings.index')->with('success', 'Hearing details updated successfully.');
    }

    // Delete record
    public function destroy($id)
    {
        DB::table('patient_hearings')->where('id', $id)->delete();
        return redirect()->route('hearings.index')->with('success', 'Hearing details Deleted successfully.');
            

    }

    // Fetch date_of_admission via AJAX
    public function getAdmissionDate($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        return response()->json([
            'date_of_admission' => $patient->date_of_admission ?? ''
        ]);
    }
}
