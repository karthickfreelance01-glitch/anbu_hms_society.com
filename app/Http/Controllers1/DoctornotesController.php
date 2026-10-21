<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctornotesController extends Controller
{
    // ✅ Index
    public function index()
    {
        $doctorNotes = DB::table('doctor_notes')
            ->join('patients', 'doctor_notes.patient_id', '=', 'patients.id')
            ->select(
                'doctor_notes.*',
                'patients.name as patient_name'
            )
            ->orderBy('doctor_notes.note_date', 'desc')
            ->get();

        return view('doctornotes.index', compact('doctorNotes'));
    }
    

    // ✅ Create form
    public function create()
    {
        $patients = DB::table('patients')->where('discharge_status','Pending')->get();
        return view('doctornotes.create', compact('patients'));
    }

    // ✅ Store
    public function store(Request $request)
{
    $request->validate([
        'patient_id' => 'required',
        'note_date' => 'required|date',
        'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048', // Max 2MB
    ]);

    $filePath = null;

    if ($request->hasFile('document')) {
        // Define the storage path
        $destinationPath = public_path('doctor-notes/doc');

        // Create the directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // Create a unique filename
        $file = $request->file('document');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Move file to the destination
        $file->move($destinationPath, $filename);

        // Store relative path (for accessing later)
        $filePath = 'doctor-notes/doc/' . $filename;
    }

    DB::table('doctor_notes')->insert([
        'patient_id' => $request->patient_id,
        'note_date' => $request->note_date,
        'height' => $request->height,
        'weight' => $request->weight,
        'bp' => $request->bp,
        'pr' => $request->pr,
        'temp' => $request->temp,
        'spo2' => $request->spo2,
        'notes' => $request->notes,
        'document' => $filePath, // Save relative file path
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['success' => true]);
}


    // ✅ Edit form
    public function edit($id)
    {
        $note = DB::table('doctor_notes')->find($id);
        $patients = DB::table('patients')->get();

        if (!$note) {
            return redirect()->route('doctornotes.index')->with('error', 'Doctor note not found.');
        }

        return view('doctornotes.edit', compact('note', 'patients'));
    }

    // ✅ Update record
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required',
            'note_date' => 'required|date',
        ]);

        DB::table('doctor_notes')->where('id', $id)->update([
            'patient_id' => $request->patient_id,
            'note_date' => $request->note_date,
            'height' => $request->height,
            'weight' => $request->weight,
            'bp' => $request->bp,
            'pr' => $request->pr,
            'temp' => $request->temp,
            'spo2' => $request->spo2,
            'notes' => $request->notes,
            'updated_at' => now(),
        ]);

        return redirect()->route('doctornotes.index')->with('success', 'Doctor note updated successfully.');
    }

    // ✅ Delete
    public function destroy($id)
    {
        DB::table('doctor_notes')->where('id', $id)->delete();
        return redirect()->route('doctornotes.index')->with('success', 'Doctor note deleted successfully.');
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
