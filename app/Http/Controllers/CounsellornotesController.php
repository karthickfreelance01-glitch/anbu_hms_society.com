<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CounsellornotesController extends Controller
{
    // ✅ Index
    public function index()
    {
        $doctorNotes = DB::table('counsellor_notes')
            ->leftjoin('patients', 'counsellor_notes.patient_id', '=', 'patients.id')
            ->leftjoin('staff', 'counsellor_notes.staff_id', '=', 'staff.id')
            ->select(
                'counsellor_notes.*',
                'patients.name as patient_name',
                'staff.staff_name'
            )
            ->orderBy('counsellor_notes.note_date', 'desc')
            ->get();

        return view('counsellornotes.index', compact('doctorNotes'));
    }
    

    // ✅ Create form
    public function create()
    {
        $patients = DB::table('patients')->where('discharge_status','Pending')->get();
        $counsellors = DB::table('staff')->get();
        return view('counsellornotes.create', compact('patients','counsellors'));
    }

    // ✅ Store
  public function store(Request $request)
{
    try {
        
        // Insert data
        DB::table('counsellor_notes')->insert([
            'patient_id' => $request->patient_id,
            'note_date' => $request->note_date,
            'staff_id' => $request->counsellor_id,
            'assess_problem' => $request->assess_problem,
            'counselling_given' => $request->counselling_given,
            'remarks' => $request->remarks,
            
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Validation errors
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Other unexpected errors
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}




    // ✅ Edit form
    public function edit($id)
    {
        $note = DB::table('counsellor_notes')->find($id);
        $patients = DB::table('patients')->where('discharge_status','Pending')->get();
        $counsellors = DB::table('staff')->get();

        if (!$note) {
            return redirect()->route('counsellornotes.index')->with('error', 'counsellor note not found.');
        }

        return view('counsellornotes.edit', compact('note', 'patients','counsellors'));
    }

    // ✅ Update record
  public function update(Request $request, $id)
{
    try {
        $request->validate([
            'patient_id' => 'required',
            'note_date' => 'required|date',
            'counsellor_id' => 'required',
            'assess_problem' => 'required|string',
            'counselling_given' => 'required|string',
        ]);

        DB::table('counsellor_notes')->where('id', $id)->update([
            'patient_id' => $request->patient_id,
            'note_date' => $request->note_date,
            'staff_id' => $request->counsellor_id,
            'assess_problem' => $request->assess_problem,
            'counselling_given' => $request->counselling_given,
            'remarks' => $request->remarks,
            'updated_at' => now(),
        ]);

        // ✅ Return JSON success for AJAX
        return response()->json(['success' => true]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // ✅ Send validation errors as JSON
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        // ✅ Send any other exception as JSON
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


    // ✅ Delete
    public function destroy($id)
    {
        DB::table('counsellor_notes')->where('id', $id)->delete();
        return redirect()->route('counsellornotes.index')->with('success', 'Doctor note deleted successfully.');
    }

   public function reports(Request $request)
{
    // Fetch all patients for dropdown
    $patients = DB::table('patients')
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

    // Build main query
    $query = DB::table('doctor_notes')
        ->join('patients', 'doctor_notes.patient_id', '=', 'patients.id')
        ->select('doctor_notes.*', 'patients.name as patient_name')
        ->orderBy('doctor_notes.note_date', 'desc');

    // Apply patient filter
    if ($request->filled('patient_id')) {
        $query->where('doctor_notes.patient_id', $request->patient_id);
    }

    // Apply from/to date filter
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('doctor_notes.note_date', [$request->from_date, $request->to_date]);
    } elseif ($request->filled('from_date')) {
        $query->whereDate('doctor_notes.note_date', '>=', $request->from_date);
    } elseif ($request->filled('to_date')) {
        $query->whereDate('doctor_notes.note_date', '<=', $request->to_date);
    }

    // Execute query
    $doctorNotes = $query->get();

    // Return view with data
    return view('doctornotes.reports', compact('doctorNotes', 'patients'));
}

}
